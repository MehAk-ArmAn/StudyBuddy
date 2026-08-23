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
    /** Extracted builds stay private and are streamed through guarded routes. */
    public const BUILD_DIRECTORY = 'studybuddy-web-apps';

    private const STAGING_DIRECTORY = 'studybuddy-app-publish';
    private const PACKAGE_DIRECTORY = 'studybuddy-app-packages';
    private const MAX_FILES = 2000;
    private const MAX_UNCOMPRESSED_BYTES = 120 * 1024 * 1024;

    /** @var array<int, string> */
    private const BLOCKED_EXTENSIONS = [
        'php', 'php3', 'php4', 'php5', 'php7', 'php8', 'phtml', 'phar',
        'cgi', 'pl', 'py', 'rb', 'sh', 'bash', 'zsh', 'exe', 'dll', 'so',
        'htaccess', 'user.ini',
    ];

    public static function buildRoot(): string
    {
        return storage_path('app/'.self::BUILD_DIRECTORY);
    }

    public static function buildDirectory(string $slug): string
    {
        return self::buildRoot().DIRECTORY_SEPARATOR.Str::slug($slug);
    }

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

        $publishRoot = self::buildRoot();
        $target = self::buildDirectory($slug);
        $operationId = (string) Str::uuid();
        $stagingRoot = storage_path('app/'.self::STAGING_DIRECTORY);
        $staging = $stagingRoot.DIRECTORY_SEPARATOR.$operationId;
        $backup = $publishRoot.DIRECTORY_SEPARATOR.'.'.$slug.'-backup-'.$operationId;
        $newPackageAbsolute = null;
        $backupCreated = false;

        try {
            $this->ensureManagedRoot($publishRoot, 'private web-build');
            $this->ensureManagedRoot($stagingRoot, 'web-build staging');
            $this->assertMissingPath($staging, 'web-build staging');
            $this->assertMissingPath($backup, 'web-build backup');
            File::ensureDirectoryExists($staging);

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

            // A `flutter build web` artefact ships `<base href="/">`, so every
            // relative asset would be requested from the site root and 404.
            // StudyBuddy re-points it at the folder the build is served from,
            // which is why one unmodified ZIP can be published under any slug.
            $this->normalizeBaseHref($staging.DIRECTORY_SEPARATOR.'index.html', $slug);

            $packageName = $slug.'-'.now()->format('Ymd-His').'-'.substr($operationId, 0, 8).'.zip';
            $packageDirectory = storage_path('app/'.self::PACKAGE_DIRECTORY);
            $packagePath = self::PACKAGE_DIRECTORY.'/'.$packageName;
            $newPackageAbsolute = $packageDirectory.DIRECTORY_SEPARATOR.$packageName;
            $this->ensureManagedRoot($packageDirectory, 'web-build package');

            if (! File::copy($archive->getRealPath(), $newPackageAbsolute)) {
                throw new RuntimeException('StudyBuddy could not retain the uploaded ZIP package.');
            }

            $this->assertManagedDirectoryOrMissing($target, 'existing web build');
            if (File::isDirectory($target)) {
                if (! $this->moveDirectory($target, $backup)) {
                    throw new RuntimeException('StudyBuddy could not prepare the previous web build for replacement.');
                }
                $backupCreated = true;
            }

            if (! $this->moveDirectory($staging, $target)) {
                throw new RuntimeException('StudyBuddy could not move the published app into private launcher storage.');
            }

            if ($backupCreated) {
                // The new build is already complete and in place. Backup cleanup
                // must not turn a successful publish into a false failure, and it
                // must never follow a swapped-in symbolic link.
                try {
                    $this->deleteManagedDirectory($backup, 'previous web-build backup');
                } catch (Throwable $cleanupException) {
                    report($cleanupException);
                }

                $backupCreated = false;
            }
        } catch (Throwable $exception) {
            $this->cleanupTemporaryDirectory($staging);

            if ($newPackageAbsolute) {
                File::delete($newPackageAbsolute);
            }

            if ($backupCreated) {
                try {
                    $this->assertManagedDirectoryOrMissing($backup, 'previous web-build backup');

                    if (
                        File::isDirectory($backup)
                        && ! file_exists($target)
                        && ! is_link($target)
                        && $this->moveDirectory($backup, $target)
                    ) {
                        $backupCreated = false;
                    }
                } catch (Throwable $restoreException) {
                    report($restoreException);
                }
            }

            if ($backupCreated) {
                // Leave the last known-good build intact for manual recovery if
                // automatic restoration fails. Never delete it in this path.
                report(new RuntimeException(
                    'StudyBuddy retained the previous web-build backup after automatic restoration failed: '.$backup
                ));
            }

            throw $exception;
        }

        return [
            'web_play_url' => '/web-apps/'.$slug.'/index.html',
            'web_app_package_path' => $packagePath,
            'web_app_entry_path' => 'web-apps/'.$slug.'/index.html',
            'web_app_uploaded_at' => now(),
        ];
    }

    /**
     * Move an already-published launcher folder when an app's slug changes.
     *
     * Without this the public /play/{slug} URL would point at a folder that no
     * longer matches, and the app would 404 after a rename.
     *
     * @return array{web_play_url:string, web_app_entry_path:string}|array{}
     */
    public function renamePublishedSlug(string $oldSlug, string $newSlug): array
    {
        $oldSlug = Str::slug($oldSlug);
        $newSlug = Str::slug($newSlug);

        if ($oldSlug === '' || $newSlug === '' || $oldSlug === $newSlug) {
            return [];
        }

        $publishRoot = self::buildRoot();
        $this->ensureManagedRoot($publishRoot, 'private web-build');

        $source = self::buildDirectory($oldSlug);
        $target = self::buildDirectory($newSlug);

        $this->assertManagedDirectoryOrMissing($source, 'existing web build');
        $this->assertManagedDirectoryOrMissing($target, 'destination web build');

        if (! File::isDirectory($source)) {
            return [];
        }

        if (File::isDirectory($target)) {
            $this->deleteManagedDirectory($target, 'destination web build');
        }

        if (! $this->moveDirectory($source, $target)) {
            throw new RuntimeException('StudyBuddy could not move the published web build to the new app address.');
        }

        return [
            'web_play_url' => '/web-apps/'.$newSlug.'/index.html',
            'web_app_entry_path' => 'web-apps/'.$newSlug.'/index.html',
        ];
    }

    public function remove(StudyBuddyMiniAppPlatform $app): void
    {
        $slug = Str::slug($app->slug ?: $app->name);

        if ($slug !== '') {
            $privateRoot = self::buildRoot();
            $this->assertRootIsNotLink($privateRoot, 'private web-build');
            $this->removeManagedBuild(self::buildDirectory($slug));

            // Compatibility cleanup for packages published before extracted
            // builds moved out of public/. New publishes never write here.
            $legacyRoot = public_path('web-apps');
            $this->assertRootIsNotLink($legacyRoot, 'legacy public web-build');
            $this->removeManagedBuild($legacyRoot.DIRECTORY_SEPARATOR.$slug);
        }

        $this->deleteStoredPackage($app->web_app_package_path);
    }

    public function deleteStoredPackage(?string $relativePath): void
    {
        if (! $relativePath) {
            return;
        }

        $normalized = ltrim(str_replace('\\', '/', $relativePath), '/');
        if (
            ! str_starts_with($normalized, self::PACKAGE_DIRECTORY.'/')
            || preg_match('#(^|/)\.\.(/|$)#', $normalized)
        ) {
            return;
        }

        $filename = substr($normalized, strlen(self::PACKAGE_DIRECTORY.'/'));
        if ($filename === '' || str_contains($filename, '/')) {
            return;
        }

        $packageRoot = storage_path('app/'.self::PACKAGE_DIRECTORY);
        $this->assertRootIsNotLink($packageRoot, 'web-build package');
        $path = $packageRoot.DIRECTORY_SEPARATOR.$filename;

        if (is_link($path)) {
            if (! @unlink($path)) {
                throw new RuntimeException('StudyBuddy could not safely remove the stored browser ZIP link.');
            }

            return;
        }

        if (File::exists($path) && ! File::delete($path)) {
            throw new RuntimeException('StudyBuddy could not remove the stored browser ZIP.');
        }
    }

    /** Kept behind a method so rollback failure paths are testable. */
    protected function moveDirectory(string $from, string $to): bool
    {
        return File::moveDirectory($from, $to);
    }

    private function ensureManagedRoot(string $path, string $label): void
    {
        $this->assertRootIsNotLink($path, $label);

        if (file_exists($path) && ! is_dir($path)) {
            throw new RuntimeException('StudyBuddy expected the '.$label.' location to be a directory.');
        }

        File::ensureDirectoryExists($path);
        $this->assertRootIsNotLink($path, $label);

        if (! is_dir($path)) {
            throw new RuntimeException('StudyBuddy could not prepare the '.$label.' directory.');
        }
    }

    private function assertRootIsNotLink(string $path, string $label): void
    {
        if (is_link($path)) {
            throw new RuntimeException('StudyBuddy refused a symbolic link at the '.$label.' location.');
        }

        if (file_exists($path) && ! is_dir($path)) {
            throw new RuntimeException('StudyBuddy expected the '.$label.' location to be a directory.');
        }
    }

    private function assertMissingPath(string $path, string $label): void
    {
        if (is_link($path) || file_exists($path)) {
            throw new RuntimeException('StudyBuddy found an unexpected path at the '.$label.' location.');
        }
    }

    private function assertManagedDirectoryOrMissing(string $path, string $label): void
    {
        if (is_link($path)) {
            throw new RuntimeException('StudyBuddy refused a symbolic link at the '.$label.' location.');
        }

        if (file_exists($path) && ! is_dir($path)) {
            throw new RuntimeException('StudyBuddy expected the '.$label.' location to be a directory.');
        }
    }

    private function deleteManagedDirectory(string $path, string $label): void
    {
        $this->assertManagedDirectoryOrMissing($path, $label);

        if (! file_exists($path)) {
            return;
        }

        File::deleteDirectory($path);

        if (file_exists($path) || is_link($path)) {
            throw new RuntimeException('StudyBuddy could not remove the '.$label.' directory.');
        }
    }

    private function cleanupTemporaryDirectory(string $path): void
    {
        try {
            $this->deleteManagedDirectory($path, 'web-build staging');
        } catch (Throwable $cleanupException) {
            report($cleanupException);
        }
    }

    private function removeManagedBuild(string $directory): void
    {
        if (is_link($directory)) {
            if (! @unlink($directory)) {
                throw new RuntimeException('StudyBuddy could not safely remove the browser build link.');
            }

            return;
        }

        if (! file_exists($directory)) {
            return;
        }

        if (! is_dir($directory)) {
            throw new RuntimeException('StudyBuddy refused to remove an unexpected browser build path.');
        }

        $this->deleteManagedDirectory($directory, 'hosted browser build');
    }

    /**
     * Point the entry document at the folder the build is actually served from.
     *
     * `flutter build web` writes `<base href="/">` unless it is told otherwise
     * at build time, so every relative asset (flutter_bootstrap.js, main.dart.js,
     * canvaskit/, assets/) would be requested from the site root and 404. Fixing
     * it here — centrally, at publish time — is what lets one unmodified ZIP be
     * published under any slug without rebuilding it.
     *
     * A small StudyBuddy bridge script is injected alongside it. It keeps the
     * base correct if the same folder is served from another mount (the Admin
     * draft preview, or after a slug rename), keeps CanvasKit local instead of
     * reaching out to a CDN, and reports genuine readiness to the launcher.
     */
    private function normalizeBaseHref(string $indexPath, string $slug): void
    {
        $html = @file_get_contents($indexPath);

        if ($html === false || trim($html) === '') {
            throw new RuntimeException('StudyBuddy could not read index.html from the uploaded web build.');
        }

        $base = '/app-builds/'.$slug.'/';
        $tag = '<base href="'.$base.'">';

        $replaced = preg_replace(
            '#<base\b[^>]*\bhref\s*=\s*(?:"[^"]*"|\'[^\']*\'|[^\s"\'>]+)[^>]*>#i',
            $tag,
            $html,
            1,
            $count
        );

        if ($replaced === null) {
            throw new RuntimeException('StudyBuddy could not read the base address in index.html.');
        }

        if ($count === 0) {
            $replaced = $this->insertIntoHead($html, $tag);
        }

        $bridge = $this->launcherBridgeScript($base, File::exists(dirname($indexPath).DIRECTORY_SEPARATOR.'canvaskit'));
        $withBridge = $this->insertAfterBaseTag($replaced, $bridge);

        if (@file_put_contents($indexPath, $withBridge) === false) {
            throw new RuntimeException('StudyBuddy could not prepare index.html for the browser launcher.');
        }
    }

    /** Put a tag inside <head>, falling back sensibly on unusual documents. */
    private function insertIntoHead(string $html, string $tag): string
    {
        foreach (['#<head\b[^>]*>#i', '#<html\b[^>]*>#i'] as $pattern) {
            $result = preg_replace($pattern, '$0'."\n    ".$tag, $html, 1, $count);

            if ($result !== null && $count === 1) {
                return $result;
            }
        }

        return $tag."\n".$html;
    }

    private function insertAfterBaseTag(string $html, string $script): string
    {
        $result = preg_replace('#<base\b[^>]*>#i', '$0'."\n".$script, $html, 1, $count);

        if ($result !== null && $count === 1) {
            return $result;
        }

        return $this->insertIntoHead($html, $script);
    }

    /**
     * The injected bridge. Deliberately tiny, dependency-free and generic: it
     * never touches the app's own JavaScript bundles or Dart source.
     */
    private function launcherBridgeScript(string $base, bool $hasLocalCanvasKit): string
    {
        $encodedBase = json_encode($base, JSON_UNESCAPED_SLASHES);
        $localCanvasKit = $hasLocalCanvasKit ? 'true' : 'false';

        return <<<HTML
    <script>
    /* Injected by StudyBuddy when this build was published. Do not edit by hand. */
    (function () {
        'use strict';

        var published = {$encodedBase};
        var localCanvasKit = {$localCanvasKit};
        var here = window.location.pathname;
        var directory;

        if (here.charAt(here.length - 1) === '/') {
            directory = here;
        } else {
            var last = here.slice(here.lastIndexOf('/') + 1);
            directory = last.indexOf('.') === -1
                ? here + '/'
                : here.slice(0, here.lastIndexOf('/') + 1);
        }

        // The Admin draft preview serves this same folder from another address,
        // and a slug rename moves the folder without rewriting the document.
        if (directory && directory !== published) {
            try {
                var tag = document.querySelector('base');

                if (!tag) {
                    tag = document.createElement('base');
                    document.head.insertBefore(tag, document.head.firstChild);
                }

                tag.setAttribute('href', directory);
            } catch (error) {
                directory = published;
            }
        }

        // Flutter asks a CDN for CanvasKit unless told the build carries its
        // own copy. Keeping it local means no third-party origin is needed.
        if (localCanvasKit) {
            try {
                var flutter = window._flutter = window._flutter || {};
                var config = flutter.buildConfig;

                Object.defineProperty(flutter, 'buildConfig', {
                    configurable: true,
                    enumerable: true,
                    get: function () {
                        return config;
                    },
                    set: function (value) {
                        if (value && typeof value === 'object') {
                            value.useLocalCanvasKit = true;
                        }

                        config = value;
                    }
                });
            } catch (error) {
                /* An older loader without buildConfig still runs normally. */
            }
        }

        var settled = false;

        var post = function (type, detail) {
            try {
                if (window.parent && window.parent !== window) {
                    var payload = {type: type, source: 'studybuddy-app'};

                    for (var key in (detail || {})) {
                        payload[key] = detail[key];
                    }

                    window.parent.postMessage(payload, window.location.origin);
                }
            } catch (error) {
                /* Messaging is an enhancement; the app still runs without it. */
            }
        };

        var ready = function () {
            if (settled === 'ready') {
                return;
            }

            settled = 'ready';
            post('studybuddy:app-ready');
        };

        var failed = function (reason) {
            if (settled) {
                return;
            }

            settled = 'failed';
            post('studybuddy:app-failed', {reason: reason});
        };

        // Flutter fires this once the first frame is actually painted, which is
        // the only honest "the learner can see it" signal available.
        window.addEventListener('flutter-first-frame', ready);

        var isFlutter = function () {
            return !!(
                window._flutter
                || document.querySelector('flutter-view, flt-glass-pane')
                || document.querySelector('script[src*="flutter_bootstrap.js"], script[src*="main.dart.js"], script[src*="flutter.js"]')
            );
        };

        var watch = function () {
            if (document.querySelector('flutter-view, flt-glass-pane')) {
                ready();
                return;
            }

            if (typeof MutationObserver !== 'function') {
                return;
            }

            var observer = new MutationObserver(function () {
                if (document.querySelector('flutter-view, flt-glass-pane')) {
                    observer.disconnect();
                    ready();
                }
            });

            observer.observe(document.documentElement, {childList: true, subtree: true});
        };

        window.addEventListener('error', function (event) {
            var target = event && event.target;

            if (target && target !== window && target.tagName === 'SCRIPT') {
                failed('script');
                return;
            }

            if (!target || target === window) {
                failed('exception');
            }
        }, true);

        window.addEventListener('DOMContentLoaded', watch);

        window.addEventListener('load', function () {
            if (!isFlutter()) {
                ready();
                return;
            }

            watch();

            // A published build has to start within a generous window, or the
            // launcher offers a retry instead of a permanent blank rectangle.
            window.setTimeout(function () {
                failed('timeout');
            }, 60000);

            // The generated Flutter service worker is deprecated and only ever
            // scoped to this folder. Clear it so nothing outlives the session.
            if (navigator.serviceWorker && navigator.serviceWorker.getRegistrations) {
                navigator.serviceWorker.getRegistrations().then(function (registrations) {
                    registrations.forEach(function (registration) {
                        var scope = '';

                        try {
                            scope = new URL(registration.scope).pathname;
                        } catch (error) {
                            return;
                        }

                        if (scope && scope.indexOf(directory) === 0) {
                            registration.unregister();
                        }
                    });
                }).catch(function () {});
            }
        });
    })();
    </script>
HTML;
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
