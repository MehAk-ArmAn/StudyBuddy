<?php

namespace App\Http\Controllers;

use App\Models\StudyBuddyMiniAppPlatform;
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

        abort_unless(
            $app->is_web_enabled,
            404
        );

        abort_if(
            preg_match('/^https?:\/\//i', (string) $app->web_play_url),
            404
        );

        $safeSlug = Str::slug($app->slug ?: $app->name);

        $base = realpath(
            public_path('web-apps/'.$safeSlug)
        );

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
            || str_contains($relative, '..')
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
            'Cross-Origin-Resource-Policy' => 'same-origin',
        ];

        if (
            in_array(
                $extension,
                ['html', 'htm'],
                true
            )
        ) {
            $headers['Cache-Control'] =
                'no-cache, no-store, must-revalidate';

            $headers['Content-Security-Policy'] =
                "default-src 'self' data: blob: https:; "
                ."script-src 'self' 'unsafe-inline' 'unsafe-eval' blob: https:; "
                ."style-src 'self' 'unsafe-inline' https:; "
                ."img-src 'self' data: blob: https:; "
                ."font-src 'self' data: https:; "
                ."media-src 'self' data: blob: https:; "
                ."connect-src 'self' data: blob: https: wss:; "
                ."worker-src 'self' blob:; "
                ."manifest-src 'self'; "
                ."frame-src 'self' https:; "
                ."object-src 'none'; "
                ."base-uri 'self'; "
                ."form-action 'self'; "
                ."frame-ancestors 'self';";
        } else {
            $headers['Cache-Control'] =
                'public, max-age=31536000, immutable';
        }

        if (
            in_array(
                $extension,
                ['js', 'mjs'],
                true
            )
        ) {
            $headers['Service-Worker-Allowed'] =
                '/app-builds/'.$safeSlug.'/';
        }

        return response()->file(
            $candidate,
            $headers
        );
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
