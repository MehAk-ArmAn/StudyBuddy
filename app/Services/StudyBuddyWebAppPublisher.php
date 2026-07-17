<?php

namespace App\Services;

use App\Models\StudyBuddyMiniAppPlatform;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\File;
use Illuminate\Support\Str;
use PharData;
use RecursiveIteratorIterator;
use RuntimeException;
use Throwable;
use ZipArchive;

class StudyBuddyWebAppPublisher
{
    private const MAX_FILES = 2000;
    private const MAX_UNCOMPRESSED_BYTES = 120 * 1024 * 1024;

    /** @var array<int, string> */
    private const BLOCKED_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar',
        'cgi', 'pl', 'py', 'rb', 'sh', 'bash', 'zsh', 'exe', 'dll', 'so',
        'htaccess', 'user.ini',
    ];

    /**
     * Publish a trusted, admin-uploaded static web application.
     *
     * The ZIP must contain an index.html file. Server-side executable files,
     * path traversal, symbolic links, and oversized archives are rejected.
     *
     * @return array{web_play_url:string, web_app_package_path:string, web_app_entry_path:string, web_app_uploaded_at:\Illuminate\Support\Carbon}
     */
    public function publish(StudyBuddyMiniAppPlatform $app, UploadedFile $archive): array
    {
        $slug = Str::slug($app->slug ?: $app->name);
        if ($slug === '') {
            throw new RuntimeException('This app needs a valid slug before a web build can be published.');
        }

        $publishRoot = public_path('web-apps');
        $target = $publishRoot.DIRECTORY_SEPARATOR.$slug;
        $operationId = (string) Str::uuid();
        $staging = storage_path('app/studybuddy-app-publish/'.$operationId);
        $backup = $publishRoot.DIRECTORY_SEPARATOR.'.'.$slug.'-backup-'.$operationId;
        $newPackageAbsolute = null;
        $backupCreated = false;

        File::ensureDirectoryExists($publishRoot);
        File::ensureDirectoryExists($staging);

        try {
            $prefix = $this->inspectRawZipArchive($archive->getRealPath());

            if (class_exists(ZipArchive::class)) {
                $this->extractWithZipArchive($archive->getRealPath(), $staging, $prefix);
            } elseif (class_exists(PharData::class)) {
                $this->extractWithPhar($archive->getRealPath(), $staging, $prefix);
            } else {
                throw new RuntimeException('ZIP publishing is unavailable on this server. Enable PHP ZipArchive or Phar support.');
            }

            if (! File::exists($staging.DIRECTORY_SEPARATOR.'index.html')) {
                throw new RuntimeException('The app ZIP must contain an index.html entry file.');
            }

            $packageName = $slug.'-'.now()->format('Ymd-His').'-'.substr($operationId, 0, 8).'.zip';
            $packageDirectory = storage_path('app/studybuddy-app-packages');
            $packagePath = 'studybuddy-app-packages/'.$packageName;
            $newPackageAbsolute = $packageDirectory.DIRECTORY_SEPARATOR.$packageName;
            File::ensureDirectoryExists($packageDirectory);

            if (! File::copy($archive->getRealPath(), $newPackageAbsolute)) {
                throw new RuntimeException('StudyBuddy could not retain the uploaded ZIP package.');
            }

            File::deleteDirectory($backup);
            if (File::exists($target)) {
                if (! File::moveDirectory($target, $backup)) {
                    throw new RuntimeException('StudyBuddy could not prepare the previous web build for replacement.');
                }
                $backupCreated = true;
            }

            if (! File::moveDirectory($staging, $target)) {
                if ($backupCreated) {
                    File::moveDirectory($backup, $target);
                    $backupCreated = false;
                }
                throw new RuntimeException('StudyBuddy could not move the published app into the public launcher folder.');
            }

            if ($backupCreated) {
                File::deleteDirectory($backup);
            }
        } catch (Throwable $exception) {
            File::deleteDirectory($staging);
            if ($newPackageAbsolute) {
                File::delete($newPackageAbsolute);
            }
            if ($backupCreated && ! File::exists($target) && File::exists($backup)) {
                File::moveDirectory($backup, $target);
            }
            File::deleteDirectory($backup);
            throw $exception;
        }

        return [
            'web_play_url' => '/web-apps/'.$slug.'/index.html',
            'web_app_package_path' => $packagePath,
            'web_app_entry_path' => 'web-apps/'.$slug.'/index.html',
            'web_app_uploaded_at' => now(),
        ];
    }

    public function remove(StudyBuddyMiniAppPlatform $app): void
    {
        $slug = Str::slug($app->slug ?: $app->name);

        if ($slug !== '') {
            File::deleteDirectory(public_path('web-apps/'.$slug));
        }

        if ($app->web_app_package_path) {
            File::delete(storage_path('app/'.ltrim($app->web_app_package_path, '/')));
        }
    }

    public function deleteStoredPackage(?string $relativePath): void
    {
        if (! $relativePath) {
            return;
        }

        $normalized = ltrim(str_replace('\\', '/', $relativePath), '/');
        if (! str_starts_with($normalized, 'studybuddy-app-packages/') || str_contains($normalized, '..')) {
            return;
        }

        File::delete(storage_path('app/'.$normalized));
    }

    private function extractWithZipArchive(string $archivePath, string $staging, string $prefix): void
    {
        $zip = new ZipArchive();
        $opened = $zip->open($archivePath);

        if ($opened !== true) {
            throw new RuntimeException('The uploaded file could not be opened as a valid ZIP archive.');
        }

        try {
            for ($index = 0; $index < $zip->numFiles; $index++) {
                $stat = $zip->statIndex($index);
                $original = (string) ($stat['name'] ?? '');
                $normalized = $this->normalizePath($original);

                if ($prefix !== '' && ! str_starts_with($normalized, $prefix)) {
                    continue;
                }

                $relative = $prefix === '' ? $normalized : substr($normalized, strlen($prefix));
                $relative = ltrim($relative, '/');

                if ($relative === '') {
                    continue;
                }

                $this->assertSafePath($relative);

                if (str_ends_with($normalized, '/')) {
                    File::ensureDirectoryExists($staging.DIRECTORY_SEPARATOR.$relative);
                    continue;
                }

                $this->assertAllowedFile($relative);
                $destination = $this->prepareDestination($staging, $relative);

                $source = $zip->getStream($original);
                if (! is_resource($source)) {
                    throw new RuntimeException('StudyBuddy could not read '.$relative.' from the ZIP archive.');
                }

                $this->copyStream($source, $destination, $relative);
            }
        } finally {
            $zip->close();
        }
    }

    private function extractWithPhar(string $archivePath, string $staging, string $prefix): void
    {
        try {
            $phar = new PharData($archivePath);
        } catch (Throwable $exception) {
            throw new RuntimeException('The uploaded file could not be opened as a valid ZIP archive.', previous: $exception);
        }

        $base = 'phar://'.str_replace('\\', '/', realpath($archivePath) ?: $archivePath).'/';
        $entries = [];
        $totalBytes = 0;

        $iterator = new RecursiveIteratorIterator($phar, RecursiveIteratorIterator::SELF_FIRST);
        foreach ($iterator as $file) {
            $sourcePath = str_replace('\\', '/', $file->getPathname());
            $relative = str_starts_with($sourcePath, $base) ? substr($sourcePath, strlen($base)) : $file->getFilename();
            $normalized = $this->normalizePath($relative);

            $this->assertSafePath($normalized);
            if (method_exists($file, 'isLink') && $file->isLink()) {
                throw new RuntimeException('Symbolic links are not allowed inside uploaded web app ZIP files.');
            }

            $isDirectory = $file->isDir();
            if (! $isDirectory) {
                $totalBytes += (int) $file->getSize();
                $this->assertArchiveSize($totalBytes);
            }

            $entries[] = [
                'source' => $sourcePath,
                'name' => $normalized,
                'directory' => $isDirectory,
            ];


            if (count($entries) > self::MAX_FILES) {
                throw new RuntimeException('The app ZIP contains too many files. Keep it below '.self::MAX_FILES.' files.');
            }
        }

        foreach ($entries as $entry) {
            $normalized = $entry['name'];
            if ($prefix !== '' && ! str_starts_with($normalized, $prefix)) {
                continue;
            }

            $relative = $prefix === '' ? $normalized : substr($normalized, strlen($prefix));
            $relative = ltrim($relative, '/');

            if ($relative === '') {
                continue;
            }

            if ($entry['directory']) {
                File::ensureDirectoryExists($staging.DIRECTORY_SEPARATOR.$relative);
                continue;
            }

            $this->assertAllowedFile($relative);
            $destination = $this->prepareDestination($staging, $relative);
            $source = fopen($entry['source'], 'rb');

            if (! is_resource($source)) {
                throw new RuntimeException('StudyBuddy could not read '.$relative.' from the ZIP archive.');
            }

            $this->copyStream($source, $destination, $relative);
        }
    }

    /**
     * Read the ZIP central directory before extraction.
     *
     * PharData can silently omit unsafe entries such as ../escape.txt. Inspecting
     * the raw directory means the entire upload is rejected instead of quietly
     * publishing only the entries Phar chooses to expose.
     */
    private function inspectRawZipArchive(string $archivePath): string
    {
        $data = file_get_contents($archivePath);
        if ($data === false || strlen($data) < 22) {
            throw new RuntimeException('The uploaded file could not be read as a valid ZIP archive.');
        }

        $length = strlen($data);
        $minimum = max(0, $length - 65557);
        $eocdOffset = null;
        $eocd = null;

        for ($offset = $length - 22; $offset >= $minimum; $offset--) {
            if (substr($data, $offset, 4) !== "PK\x05\x06") {
                continue;
            }

            $candidate = unpack(
                'vdisk/vdirectory_disk/ventries_on_disk/ventries/Vdirectory_size/Vdirectory_offset/vcomment_length',
                substr($data, $offset + 4, 18)
            );

            if (is_array($candidate) && $offset + 22 + (int) $candidate['comment_length'] === $length) {
                $eocdOffset = $offset;
                $eocd = $candidate;
                break;
            }
        }

        if ($eocdOffset === null || ! is_array($eocd)) {
            throw new RuntimeException('The uploaded file does not contain a valid ZIP directory.');
        }

        if ((int) $eocd['disk'] !== 0 || (int) $eocd['directory_disk'] !== 0) {
            throw new RuntimeException('Multi-part ZIP archives are not supported.');
        }

        $entryCount = (int) $eocd['entries'];
        $directoryOffset = (int) $eocd['directory_offset'];
        $directorySize = (int) $eocd['directory_size'];

        if ($entryCount === 0 || $entryCount === 0xffff || $directoryOffset === 0xffffffff || $directorySize === 0xffffffff) {
            throw new RuntimeException('Empty or ZIP64 web-app archives are not supported.');
        }

        if ($entryCount > self::MAX_FILES) {
            throw new RuntimeException('The app ZIP contains too many files. Keep it below '.self::MAX_FILES.' files.');
        }

        if ($directoryOffset < 0 || $directorySize < 0 || $directoryOffset + $directorySize > $eocdOffset) {
            throw new RuntimeException('The ZIP central directory is invalid.');
        }

        $cursor = $directoryOffset;
        $totalBytes = 0;
        $indexCandidates = [];
        $seen = [];

        for ($index = 0; $index < $entryCount; $index++) {
            if ($cursor + 46 > $length || substr($data, $cursor, 4) !== "PK\x01\x02") {
                throw new RuntimeException('The ZIP central directory contains a damaged entry.');
            }

            $entry = unpack(
                'vversion_made/vversion_needed/vflags/vmethod/vmodified_time/vmodified_date/Vcrc/Vcompressed_size/Vuncompressed_size/vname_length/vextra_length/vcomment_length/vdisk_start/vinternal_attributes/Vexternal_attributes/Vlocal_header_offset',
                substr($data, $cursor + 4, 42)
            );

            if (! is_array($entry)) {
                throw new RuntimeException('StudyBuddy could not inspect a ZIP entry.');
            }

            $nameLength = (int) $entry['name_length'];
            $extraLength = (int) $entry['extra_length'];
            $commentLength = (int) $entry['comment_length'];
            $recordLength = 46 + $nameLength + $extraLength + $commentLength;

            if ($nameLength < 1 || $cursor + $recordLength > $length) {
                throw new RuntimeException('The ZIP contains an invalid filename record.');
            }

            $originalName = substr($data, $cursor + 46, $nameLength);
            $normalized = $this->normalizePath($originalName);
            $this->assertSafePath($normalized);

            if (((int) $entry['flags'] & 0x0001) !== 0) {
                throw new RuntimeException('Password-protected ZIP entries are not supported.');
            }

            $fileType = ((int) $entry['external_attributes'] >> 16) & 0170000;
            if ($fileType === 0120000) {
                throw new RuntimeException('Symbolic links are not allowed inside uploaded web app ZIP files.');
            }

            $key = strtolower(rtrim($normalized, '/'));
            if ($key === '' || isset($seen[$key])) {
                throw new RuntimeException('The ZIP contains an empty or duplicate file path.');
            }
            $seen[$key] = true;

            $isDirectory = str_ends_with($normalized, '/');
            if (! $isDirectory) {
                $this->assertAllowedFile($normalized);
                $totalBytes += (int) $entry['uncompressed_size'];
                $this->assertArchiveSize($totalBytes);

                if (strtolower(basename($normalized)) === 'index.html') {
                    $indexCandidates[] = $normalized;
                }
            }

            $cursor += $recordLength;
        }

        if ($cursor > $directoryOffset + $directorySize) {
            throw new RuntimeException('The ZIP central directory size is inconsistent.');
        }

        return $this->entryPrefix($indexCandidates);
    }

    /** @param array<int, string> $indexCandidates */
    private function entryPrefix(array $indexCandidates): string
    {
        if ($indexCandidates === []) {
            throw new RuntimeException('No index.html file was found in the uploaded web app ZIP.');
        }

        usort($indexCandidates, static fn (string $left, string $right): int => substr_count($left, '/') <=> substr_count($right, '/'));

        $directory = dirname($indexCandidates[0]);
        return $directory === '.' ? '' : trim($directory, '/').'/';
    }

    private function prepareDestination(string $staging, string $relative): string
    {
        $destination = $staging.DIRECTORY_SEPARATOR.str_replace('/', DIRECTORY_SEPARATOR, $relative);
        File::ensureDirectoryExists(dirname($destination));
        return $destination;
    }

    /** @param resource $source */
    private function copyStream($source, string $destination, string $relative): void
    {
        $output = fopen($destination, 'wb');
        if (! is_resource($output)) {
            fclose($source);
            throw new RuntimeException('StudyBuddy could not publish '.$relative.'.');
        }

        stream_copy_to_stream($source, $output);
        fclose($source);
        fclose($output);
    }

    private function assertArchiveSize(int $totalBytes): void
    {
        if ($totalBytes > self::MAX_UNCOMPRESSED_BYTES) {
            throw new RuntimeException('The extracted web app is too large. Keep it below 120 MB.');
        }
    }

    private function assertAllowedFile(string $relative): void
    {
        $extension = strtolower(pathinfo($relative, PATHINFO_EXTENSION));
        $basename = strtolower(basename($relative));

        if (in_array($extension, self::BLOCKED_EXTENSIONS, true) || in_array($basename, ['.htaccess', '.user.ini'], true)) {
            throw new RuntimeException('The ZIP contains a blocked executable or server configuration file: '.$relative);
        }
    }

    private function normalizePath(string $path): string
    {
        if (preg_match('#^[\\/]#', $path) || preg_match('#^[A-Za-z]:[\\/]#', $path)) {
            throw new RuntimeException('The ZIP contains an absolute file path.');
        }

        $path = str_replace('\\', '/', $path);
        $directory = str_ends_with($path, '/');
        $segments = [];

        foreach (explode('/', $path) as $segment) {
            if ($segment === '' || $segment === '.') {
                continue;
            }
            $segments[] = $segment;
        }

        $normalized = implode('/', $segments);
        return $directory && $normalized !== '' ? $normalized.'/' : $normalized;
    }

    private function assertSafePath(string $path): void
    {
        if ($path === '' || preg_match('/[\x00-\x1F\x7F]/', $path) || preg_match('#(^|/)\.\.(/|$)#', $path)) {
            throw new RuntimeException('The ZIP contains an unsafe file path.');
        }

        if (str_contains($path, ':') || preg_match('#^[A-Za-z]:/#', $path)) {
            throw new RuntimeException('The ZIP contains a path that is not portable across supported servers.');
        }
    }

    private function assertZipEntryNotSymlink(ZipArchive $zip, int $index): void
    {
        $operatingSystem = 0;
        $attributes = 0;

        if (! $zip->getExternalAttributesIndex($index, $operatingSystem, $attributes)) {
            return;
        }

        $fileType = ($attributes >> 16) & 0170000;
        if ($fileType === 0120000) {
            throw new RuntimeException('Symbolic links are not allowed inside uploaded web app ZIP files.');
        }
    }
}
