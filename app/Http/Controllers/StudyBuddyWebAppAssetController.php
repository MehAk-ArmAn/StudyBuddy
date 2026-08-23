<?php

namespace App\Http\Controllers;

use App\Models\StudyBuddyMiniAppPlatform;
use App\Services\StudyBuddyWebAppPublisher;
use Illuminate\Http\Response;
use Illuminate\Support\Str;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StudyBuddyWebAppAssetController extends Controller
{
    public function __invoke(
        string $slug,
        ?string $path = null
    ): BinaryFileResponse|Response {
        $app = StudyBuddyMiniAppPlatform::query()
            ->active()
            ->where('slug', $slug)
            ->firstOrFail();

        return $this->serve($app, $path, false);
    }

    /** Serve draft build assets only inside the authenticated Admin preview. */
    public function preview(
        StudyBuddyMiniAppPlatform $app,
        ?string $path = null
    ): BinaryFileResponse|Response {
        return $this->serve($app, $path, true);
    }

    private function serve(
        StudyBuddyMiniAppPlatform $app,
        ?string $path = null,
        bool $adminPreview = false
    ): BinaryFileResponse|Response {

        abort_unless(
            $app->is_web_enabled,
            404
        );

        abort_if(
            preg_match('/^https?:\/\//i', (string) $app->web_play_url),
            404
        );

        $safeSlug = Str::slug($app->slug ?: $app->name);

        $buildRoot = StudyBuddyWebAppPublisher::buildRoot();
        $buildDirectory = StudyBuddyWebAppPublisher::buildDirectory($safeSlug);

        abort_if(is_link($buildRoot) || is_link($buildDirectory), 404);

        $base = realpath($buildDirectory);

        abort_unless(
            $base && is_dir($base),
            404
        );

        $relative = rawurldecode(
            trim(
                str_replace(
                    '\\',
                    '/',
                    $path ?: 'index.html'
                ),
                '/'
            )
        );

        abort_if(
            $relative === ''
            || str_contains($relative, "\0")
            || preg_match('#(^|/)\.\.(/|$)#', $relative)
            || str_starts_with($relative, '/'),
            404
        );

        $candidate = realpath(
            $base.DIRECTORY_SEPARATOR.$relative
        );

        if (
            $candidate
            && is_dir($candidate)
        ) {
            $candidate = realpath(
                $candidate.DIRECTORY_SEPARATOR.'index.html'
            );
        }

        if (
            ! $candidate
            || ! $this->insideBase($candidate, $base)
            || ! is_file($candidate)
        ) {
            if (
                ! str_contains(
                    basename($relative),
                    '.'
                )
            ) {
                $candidate = realpath(
                    $base.DIRECTORY_SEPARATOR.'index.html'
                );
            }
        }

        abort_unless(
            $candidate
            && $this->insideBase($candidate, $base)
            && is_file($candidate),
            404
        );

        $extension = Str::lower(
            pathinfo($candidate, PATHINFO_EXTENSION)
        );

        $mimeTypes = [
            'html' => 'text/html; charset=UTF-8',
            'htm' => 'text/html; charset=UTF-8',
            'css' => 'text/css; charset=UTF-8',
            'js' => 'text/javascript; charset=UTF-8',
            'mjs' => 'text/javascript; charset=UTF-8',
            'json' => 'application/json; charset=UTF-8',
            'map' => 'application/json; charset=UTF-8',
            'svg' => 'image/svg+xml',
            'png' => 'image/png',
            'jpg' => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'gif' => 'image/gif',
            'webp' => 'image/webp',
            'avif' => 'image/avif',
            'ico' => 'image/x-icon',
            'woff' => 'font/woff',
            'woff2' => 'font/woff2',
            'ttf' => 'font/ttf',
            'otf' => 'font/otf',
            'mp3' => 'audio/mpeg',
            'wav' => 'audio/wav',
            'ogg' => 'audio/ogg',
            'mp4' => 'video/mp4',
            'webm' => 'video/webm',
            'wasm' => 'application/wasm',
            'pdf' => 'application/pdf',
            'txt' => 'text/plain; charset=UTF-8',
            'xml' => 'application/xml; charset=UTF-8',
            'webmanifest' => 'application/manifest+json',
        ];

        $headers = [
            'Content-Type' => $mimeTypes[$extension]
                ?? 'application/octet-stream',
            'X-Content-Type-Options' => 'nosniff',
            // Builds are framed from StudyBuddy's own origin, so nothing here
            // ever needs to be readable by a third-party document.
            'Cross-Origin-Resource-Policy' => 'same-origin',
        ];

        $isHtml = in_array($extension, ['html', 'htm'], true);

        $headers['Cache-Control'] = $adminPreview
            ? 'private, no-store, max-age=0'
            : ($isHtml
                ? 'private, no-cache, no-store, must-revalidate'
                : 'public, max-age=0, must-revalidate');

        if ($isHtml) {
            // Uploaded builds are self-contained: everything they need was in
            // the ZIP. Keeping this to our own origin means a hosted app cannot
            // quietly pull code, fonts or telemetry from somewhere else.
            // 'wasm-unsafe-eval' is what lets CanvasKit compile its WebAssembly.
            $headers['Content-Security-Policy'] =
                "default-src 'self' data: blob:; "
                ."script-src 'self' 'unsafe-inline' 'unsafe-eval' 'wasm-unsafe-eval' blob:; "
                ."style-src 'self' 'unsafe-inline'; "
                ."img-src 'self' data: blob:; "
                // CanvasKit downloads its text fallback (Roboto and the Noto
                // symbol faces) from Google's official font host. Nothing else
                // off-origin is permitted.
                ."font-src 'self' data: https://fonts.gstatic.com; "
                ."media-src 'self' data: blob:; "
                ."connect-src 'self' data: blob: https://fonts.gstatic.com; "
                ."worker-src 'self' blob:; "
                ."child-src 'self' blob:; "
                ."manifest-src 'self'; "
                ."frame-src 'self'; "
                ."object-src 'none'; "
                ."base-uri 'self'; "
                ."form-action 'self'; "
                ."frame-ancestors 'self';";
        }

        // No Service-Worker-Allowed header: a mini-app service worker is then
        // capped at its own build folder and can never claim StudyBuddy pages.

        if ($isHtml) {
            return $this->entryDocument($candidate, $headers);
        }

        return new BinaryFileResponse(
            $candidate,
            200,
            $headers,
            ! $adminPreview
        );
    }

    /**
     * Serve an entry document with its base address matching this mount.
     *
     * Publishing already writes the public `/app-builds/{slug}/` base into the
     * file. The Admin draft preview serves the very same folder from a
     * different address, and a slug rename moves a folder without rewriting it,
     * so the value is re-pointed here rather than trusting what is on disk.
     * This also repairs builds published before any of that existed: they pick
     * up the launcher bridge on the way out instead of needing a re-upload.
     *
     * @param array<string, string> $headers
     */
    private function entryDocument(
        string $candidate,
        array $headers
    ): BinaryFileResponse|Response {
        $html = @file_get_contents($candidate);

        if ($html === false) {
            abort(404);
        }

        return response(
            StudyBuddyWebAppPublisher::prepareEntryDocument(
                $html,
                $this->mountDirectory(basename($candidate)),
                StudyBuddyWebAppPublisher::hasLocalCanvasKit(dirname($candidate))
            ),
            200,
            $headers
        );
    }

    /** The URL directory this request is serving the build from. */
    private function mountDirectory(string $filename): string
    {
        $path = rawurldecode(
            (string) parse_url(request()->getRequestUri(), PHP_URL_PATH)
        );

        if ($path === '') {
            $path = '/';
        }

        $segments = explode('/', $path);
        $last = end($segments);

        if ($last !== '' && $last === $filename) {
            return substr($path, 0, -strlen($last));
        }

        return str_ends_with($path, '/') ? $path : $path.'/';
    }

    private function insideBase(
        string $candidate,
        string $base
    ): bool {
        $base = rtrim(
            str_replace('\\', '/', $base),
            '/'
        );

        $candidate = str_replace(
            '\\',
            '/',
            $candidate
        );

        return $candidate === $base
            || str_starts_with(
                $candidate,
                $base.'/'
            );
    }
}
