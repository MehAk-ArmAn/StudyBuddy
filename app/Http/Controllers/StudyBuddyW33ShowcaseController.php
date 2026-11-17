<?php

namespace App\Http\Controllers;

use App\Models\StudyBuddyMiniAppPlatform;
use App\Services\StudyBuddyW33DesktopKit;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\BinaryFileResponse;

class StudyBuddyW33ShowcaseController extends Controller
{
    private const DISK = 'local';
    private const ROOT = 'studybuddy-w33';
    private const PLATFORMS = ['windows', 'mac', 'linux'];

    public function edit(StudyBuddyMiniAppPlatform $app): View
    {
        return view('studybuddy.w33.editor', [
            'app' => $app,
            'showcase' => DB::table('studybuddy_w33_showcases')->where('app_id', $app->id)->first(),
            'shots' => DB::table('studybuddy_w33_screenshots')->where('app_id', $app->id)->orderBy('position')->orderBy('id')->get(),
            'packages' => DB::table('studybuddy_w33_desktop_packages')->where('app_id', $app->id)->get()->keyBy('platform'),
        ]);
    }

    public function update(Request $request, StudyBuddyMiniAppPlatform $app): RedirectResponse
    {
        $data = $request->validate([
            'version' => ['nullable', 'string', 'max:40'],
            'whats_new' => ['nullable', 'string', 'max:8000'],
            'why_play' => ['nullable', 'string', 'max:4000'],
            'highlights' => ['nullable', 'string', 'max:4000'],
            'privacy_summary' => ['nullable', 'string', 'max:4000'],
            'trailer_url' => ['nullable', 'url', 'max:500', 'starts_with:https://'],
        ]);
        $now = now();
        $existing = DB::table('studybuddy_w33_showcases')->where('app_id', $app->id)->exists();
        if ($existing) {
            DB::table('studybuddy_w33_showcases')->where('app_id', $app->id)->update($data + ['updated_at' => $now]);
        } else {
            DB::table('studybuddy_w33_showcases')->insert($data + [
                'app_id' => $app->id, 'created_at' => $now, 'updated_at' => $now,
            ]);
        }
        return back()->with('status', 'Showcase saved. Published listings update instantly.');
    }

    public function uploadScreenshots(Request $request, StudyBuddyMiniAppPlatform $app): RedirectResponse
    {
        $data = $request->validate([
            'screenshots' => ['required', 'array', 'min:1', 'max:8'],
            'screenshots.*' => ['required', 'image', 'mimes:png,jpg,jpeg,webp', 'max:5120'],
        ]);
        $count = DB::table('studybuddy_w33_screenshots')->where('app_id', $app->id)->count();
        if ($count + count($data['screenshots']) > 24) {
            throw ValidationException::withMessages(['screenshots' => 'Maximum 24 screenshots per app.']);
        }
        $disk = Storage::disk(self::DISK);
        $paths = [];
        try {
            foreach ($request->file('screenshots') as $i => $upload) {
                $meta = @getimagesize($upload->getRealPath());
                if (! $meta || $meta[0] < 160 || $meta[1] < 160 || $meta[0] > 8192 || $meta[1] > 8192) {
                    throw ValidationException::withMessages(['screenshots' => 'Screenshots must be valid images between 160 and 8192 pixels per side.']);
                }
                $ext = match ($meta['mime'] ?? '') {
                    'image/png' => 'png', 'image/jpeg' => 'jpg', 'image/webp' => 'webp',
                    default => null,
                };
                if (! $ext) {
                    throw ValidationException::withMessages(['screenshots' => 'Only real PNG, JPG or WebP images are accepted.']);
                }
                $file = (string) Str::uuid().'.'.$ext;
                $path = $upload->storeAs(self::ROOT.'/screenshots/'.$app->id, $file, self::DISK);
                if (! $path) {
                    throw new \RuntimeException('Screenshot could not be stored.');
                }
                $paths[] = $path;
                DB::table('studybuddy_w33_screenshots')->insert([
                    'app_id' => $app->id, 'path' => $path, 'width' => $meta[0], 'height' => $meta[1],
                    'position' => $count + $i, 'title' => 'Screenshot '.($count + $i + 1),
                    'alt_text' => $app->name.' — screenshot '.($count + $i + 1),
                    'created_at' => now(), 'updated_at' => now(),
                ]);
            }
        } catch (\Throwable $e) {
            DB::table('studybuddy_w33_screenshots')->whereIn('path', $paths)->delete();
            foreach ($paths as $path) { $disk->delete($path); }
            throw $e;
        }
        return back()->with('status', 'Screenshots uploaded to the private media library.');
    }

    public function updateScreenshot(Request $request, StudyBuddyMiniAppPlatform $app, int $shot): RedirectResponse
    {
        $this->ownedShot($app, $shot);
        $data = $request->validate([
            'title' => ['nullable', 'string', 'max:120'],
            'alt_text' => ['nullable', 'string', 'max:220'],
            'position' => ['required', 'integer', 'min:0', 'max:1000'],
        ]);
        DB::table('studybuddy_w33_screenshots')->where('app_id', $app->id)->where('id', $shot)
            ->update($data + ['updated_at' => now()]);
        return back()->with('status', 'Screenshot details updated.');
    }

    public function deleteScreenshot(StudyBuddyMiniAppPlatform $app, int $shot): RedirectResponse
    {
        $media = $this->ownedShot($app, $shot);
        DB::table('studybuddy_w33_screenshots')->where('id', $media->id)->delete();
        Storage::disk(self::DISK)->delete($media->path);
        return back()->with('status', 'Screenshot removed.');
    }

    public function adminImage(StudyBuddyMiniAppPlatform $app, int $shot): BinaryFileResponse
    {
        return $this->imageResponse($this->ownedShot($app, $shot));
    }

    public function publicImage(string $slug, int $shot): BinaryFileResponse
    {
        $app = StudyBuddyMiniAppPlatform::query()->active()->where('slug', $slug)->firstOrFail();
        abort_unless($app->visibleForRole(auth()->user()?->role), 404);
        return $this->imageResponse($this->ownedShot($app, $shot));
    }

    public function uploadDesktop(Request $request, StudyBuddyMiniAppPlatform $app, string $platform): RedirectResponse
    {
        $this->validPlatform($platform);
        $data = $request->validate([
            'desktop_file' => ['required', 'file', 'max:204800'],
            'release_version' => ['nullable', 'string', 'max:40'],
        ]);
        $upload = $request->file('desktop_file');
        $ext = strtolower($upload->getClientOriginalExtension());
        $allow = [
            'windows' => ['exe', 'msix', 'msi', 'zip'],
            'mac' => ['dmg', 'pkg', 'zip'],
            'linux' => ['appimage', 'deb', 'rpm', 'zip'],
        ];
        if (! in_array($ext, $allow[$platform], true)) {
            throw ValidationException::withMessages(['desktop_file' => 'Unsupported installer type for '.$platform.'.']);
        }
        $name = basename(str_replace('\\', '/', $upload->getClientOriginalName()));
        $name = trim(preg_replace('/[^A-Za-z0-9._ -]+/', '-', $name), ' .');
        $name = substr($name ?: ('StudyBuddy-'.$platform.'.'.$ext), 0, 190);
        $path = $upload->storeAs(self::ROOT.'/desktop/'.$app->id, (string) Str::uuid().'.'.$ext, self::DISK);
        if (! $path) {
            throw new \RuntimeException('Could not save desktop package.');
        }
        $old = DB::table('studybuddy_w33_desktop_packages')->where('app_id', $app->id)->where('platform', $platform)->first();
        try {
            DB::table('studybuddy_w33_desktop_packages')->updateOrInsert(
                ['app_id' => $app->id, 'platform' => $platform],
                ['path' => $path, 'original_name' => $name, 'bytes' => $upload->getSize(),
                 'version' => $data['release_version'] ?? null, 'sha256' => hash_file('sha256', $upload->getRealPath()), 'updated_at' => now(), 'created_at' => now()]
            );
        } catch (\Throwable $e) {
            Storage::disk(self::DISK)->delete($path);
            throw $e;
        }
        if ($old) { Storage::disk(self::DISK)->delete($old->path); }
        return back()->with('status', ucfirst($platform).' desktop package is ready. The public download button appears when the app is published.');
    }

    public function deleteDesktop(StudyBuddyMiniAppPlatform $app, string $platform): RedirectResponse
    {
        $this->validPlatform($platform);
        $old = DB::table('studybuddy_w33_desktop_packages')->where('app_id', $app->id)->where('platform', $platform)->first();
        if ($old) {
            DB::table('studybuddy_w33_desktop_packages')->where('id', $old->id)->delete();
            Storage::disk(self::DISK)->delete($old->path);
        }
        return back()->with('status', ucfirst($platform).' desktop package removed.');
    }

    public function downloadDesktop(string $slug, string $platform): BinaryFileResponse
    {
        $this->validPlatform($platform);
        $app = StudyBuddyMiniAppPlatform::query()->active()->where('slug', $slug)->firstOrFail();
        abort_unless($app->is_download_enabled && $app->visibleForRole(auth()->user()?->role), 404);
        $file = DB::table('studybuddy_w33_desktop_packages')->where('app_id', $app->id)->where('platform', $platform)->first();
        abort_unless($file && Storage::disk(self::DISK)->exists($file->path), 404);
        return response()->download(Storage::disk(self::DISK)->path($file->path), $file->original_name, [
            'Content-Type' => 'application/octet-stream', 'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
        ]);
    }

    public function installDesktop(string $slug): RedirectResponse
    {
        $app = StudyBuddyMiniAppPlatform::query()->active()->where('slug', $slug)->firstOrFail();
        abort_unless($app->is_web_enabled && $app->usesUploadedBuild() && $app->hasPublishedWebApp()
            && $app->visibleForRole(auth()->user()?->role), 404);
        return redirect('/app-builds/'.rawurlencode($app->slug).'/index.html?sb33install=1');
    }

    public function desktopKit(StudyBuddyMiniAppPlatform $app, StudyBuddyW33DesktopKit $builder): BinaryFileResponse
    {
        abort_unless($app->usesUploadedBuild() && $app->hasPublishedWebApp(), 404);
        $zip = $builder->create($app);
        return response()->download($zip, 'StudyBuddy-'.Str::slug($app->name).'-desktop-builder.zip', [
            'Content-Type' => 'application/zip', 'Cache-Control' => 'private, no-store',
        ])->deleteFileAfterSend(true);
    }

    private function ownedShot(StudyBuddyMiniAppPlatform $app, int $id): object
    {
        return DB::table('studybuddy_w33_screenshots')->where('app_id', $app->id)->where('id', $id)->firstOrFail();
    }

    private function imageResponse(object $shot): BinaryFileResponse
    {
        $disk = Storage::disk(self::DISK);
        abort_unless(str_starts_with($shot->path, self::ROOT.'/screenshots/') && $disk->exists($shot->path), 404);
        return response()->file($disk->path($shot->path), [
            'Content-Type' => match (strtolower(pathinfo($shot->path, PATHINFO_EXTENSION))) {
                'png' => 'image/png', 'webp' => 'image/webp', default => 'image/jpeg',
            },
            'X-Content-Type-Options' => 'nosniff',
            'Cache-Control' => 'private, no-store',
            'Content-Security-Policy' => "default-src 'none'; sandbox",
        ]);
    }

    private function validPlatform(string $platform): void
    {
        abort_unless(in_array($platform, self::PLATFORMS, true), 404);
    }
}
