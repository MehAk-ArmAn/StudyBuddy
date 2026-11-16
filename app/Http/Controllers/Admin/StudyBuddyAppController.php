<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StudyBuddyAppRequest;
use App\Models\StudyBuddyMiniAppPlatform;
use App\Services\StudyBuddyWebAppPublisher;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * The StudyBuddy Apps CMS.
 *
 * This is the screen an admin uses to add, edit, publish and retire learning
 * apps without touching code. Everything it writes goes to
 * `studybuddy_mini_app_platforms`, which is the same table the public /apps
 * pages read from, so a save here is visible publicly straight away.
 */
class StudyBuddyAppController extends Controller
{
    /** Where uploaded app artwork lives on the public disk. */
    private const ARTWORK_DIRECTORY = 'studybuddy/apps';

    /**
     * Searchable, filterable, paginated list of every app.
     *
     * Access is enforced by the ['auth', 'admin'] middleware on the route group.
     */
    public function index(Request $request): View
    {
        $search = trim((string) $request->query('q', ''));
        $status = trim((string) $request->query('status', ''));
        $visibility = trim((string) $request->query('visibility', ''));

        $apps = StudyBuddyMiniAppPlatform::query()
            ->when($search !== '', function ($query) use ($search): void {
                $query->where(function ($inner) use ($search): void {
                    foreach (['name', 'slug', 'category', 'tagline'] as $column) {
                        $inner->orWhere($column, 'like', '%'.$search.'%');
                    }
                });
            })
            ->when(
                in_array($status, StudyBuddyAppRequest::STATUSES, true),
                fn ($query) => $query->where('status', $status)
            )
            ->when($visibility === 'published', fn ($query) => $query->where('is_active', true))
            ->when($visibility === 'hidden', fn ($query) => $query->where('is_active', false))
            ->when($visibility === 'featured', fn ($query) => $query->where('is_featured', true))
            ->ordered()
            ->simplePaginate(12)
            ->withQueryString();

        return view('admin.apps.index', [
            'apps' => $apps,
            'search' => $search,
            'status' => $status,
            'visibility' => $visibility,
            'statuses' => StudyBuddyAppRequest::STATUSES,
            'statusLabels' => StudyBuddyAppRequest::STATUS_LABELS,
            'totals' => [
                'all' => StudyBuddyMiniAppPlatform::count(),
                'published' => StudyBuddyMiniAppPlatform::where('is_active', true)->count(),
                'hidden' => StudyBuddyMiniAppPlatform::where('is_active', false)->count(),
                'featured' => StudyBuddyMiniAppPlatform::where('is_featured', true)->count(),
            ],
        ]);
    }

    public function create(): View
    {
        // An unsaved model means the create and edit screens can share one form.
        $app = new StudyBuddyMiniAppPlatform([
            'status' => 'planned',
            'category' => 'Learning',
            'accent' => 'cosmic',
            'points_reward' => 25,
            'estimated_minutes' => 10,
            'is_active' => false,
            // Store buttons only render when a URL exists, so enabling this by
            // default removes a needless extra step without exposing dead links.
            'is_download_enabled' => true,
            'audience_roles' => StudyBuddyAppRequest::ROLES,
            'sort_order' => (int) StudyBuddyMiniAppPlatform::max('sort_order') + 1,
        ]);

        return view('admin.apps.form', $this->formData($app));
    }

    public function store(StudyBuddyAppRequest $request, StudyBuddyWebAppPublisher $publisher): RedirectResponse
    {
        $app = new StudyBuddyMiniAppPlatform();
        $publishAfterBuild = $request->hasFile('web_app_zip') && $request->boolean('is_active');

        try {
            $attributes = $this->attributesFrom($request, $app);

            // A new ZIP-backed app remains private until its archive has been
            // fully inspected and moved into place. Invalid uploads therefore
            // never create even a brief public listing.
            if ($publishAfterBuild) {
                $attributes['is_active'] = false;
            }

            $app->fill($attributes);
            $app->save();

            // The web build is published after the row exists, because the
            // publisher names the launcher folder after the saved slug.
            $this->syncWebBuild($request, $app, $publisher);

            if ($publishAfterBuild) {
                $app->update(['is_active' => true]);
            }
        } catch (\Throwable $exception) {
            // A failed first ZIP must never leave behind an accidental listing,
            // uploaded artwork or half-created launcher. The publisher cleans
            // its own staging area; this finishes the new-app rollback.
            if ($app->exists) {
                $publisher->remove($app);
            }
            $this->deleteUploadedArtwork($app->hero_image);
            $this->deleteUploadedArtwork($app->image_url);

            if ($app->exists) {
                $app->delete();
            }

            throw $exception;
        }

        return redirect()
            ->route('admin.control-room.apps.edit', $app)
            ->with('status', $app->name.' was created. '.($app->is_active
                ? 'It is live on the Apps page now.'
                : 'It stays hidden until you publish it.'));
    }

    public function edit(StudyBuddyMiniAppPlatform $app): View
    {
        return view('admin.apps.form', $this->formData($app));
    }

    public function update(
        StudyBuddyAppRequest $request,
        StudyBuddyMiniAppPlatform $app,
        StudyBuddyWebAppPublisher $publisher
    ): RedirectResponse {
        $previousSlug = $app->slug;
        $previousBrowserApp = clone $app;
        $hadManagedBuild = filled($app->web_app_entry_path);
        $switchingToExternal = $hadManagedBuild
            && $request->filled('web_play_url')
            && preg_match('/^https:\/\//i', (string) $request->input('web_play_url'));
        $removingBuild = $request->boolean('remove_web_app');
        $activateAfterBuild = $request->hasFile('web_app_zip')
            && $request->boolean('is_active')
            && ! $app->is_active;

        $attributes = $this->attributesFrom($request, $app);
        if ($activateAfterBuild) {
            $attributes['is_active'] = false;
        }

        $app->fill($attributes);

        // Renaming the slug changes the public URL and the launcher folder, so
        // the already-published build has to move with it.
        if (
            $app->isDirty('slug')
            && $hadManagedBuild
            && ! $switchingToExternal
            && ! $removingBuild
        ) {
            $app->forceFill($publisher->renamePublishedSlug($previousSlug, $app->slug));
        }

        $app->save();

        $this->syncWebBuild(
            $request,
            $app,
            $publisher,
            $hadManagedBuild ? $previousBrowserApp : null
        );

        if ($activateAfterBuild) {
            $app->update(['is_active' => true]);
        }

        return redirect()
            ->route('admin.control-room.apps.edit', $app)
            ->with('status', 'Saved. '.$app->name.' is up to date.');
    }

    /**
     * Publish / unpublish without opening the whole form.
     */
    public function togglePublish(StudyBuddyMiniAppPlatform $app): RedirectResponse
    {
        $app->update(['is_active' => ! $app->is_active]);

        return back()->with('status', $app->is_active
            ? $app->name.' is now visible on the public Apps page.'
            : $app->name.' is hidden from the public Apps page.');
    }

    public function toggleFeatured(StudyBuddyMiniAppPlatform $app): RedirectResponse
    {
        $app->update(['is_featured' => ! $app->is_featured]);

        return back()->with('status', $app->is_featured
            ? $app->name.' is now featured.'
            : $app->name.' is no longer featured.');
    }

    /**
     * Save a new display order from the list screen.
     */
    public function reorder(Request $request): RedirectResponse
    {
        $data = $request->validate([
            'order' => ['required', 'array'],
            'order.*' => ['integer', 'min:0', 'max:9999'],
        ]);

        foreach ($data['order'] as $id => $position) {
            StudyBuddyMiniAppPlatform::whereKey($id)->update(['sort_order' => $position]);
        }

        return back()->with('status', 'App order saved.');
    }

    /**
     * Delete an app and everything it owns: launcher folder, stored ZIP and
     * uploaded artwork. Guarded by a typed-name confirmation in the UI.
     */
    public function destroy(Request $request, StudyBuddyMiniAppPlatform $app, StudyBuddyWebAppPublisher $publisher): RedirectResponse
    {
        $request->validate(
            ['confirm_name' => ['required', 'string']],
            ['confirm_name.required' => 'Type the app name to confirm deletion.']
        );

        if (trim((string) $request->input('confirm_name')) !== $app->name) {
            throw ValidationException::withMessages([
                'confirm_name' => 'That name does not match. Type "'.$app->name.'" exactly to delete this app.',
            ]);
        }

        $name = $app->name;

        $publisher->remove($app);
        $this->deleteUploadedArtwork($app->hero_image);
        $this->deleteUploadedArtwork($app->image_url);
        $app->delete();

        return redirect()
            ->route('admin.control-room.apps.index')
            ->with('status', $name.' was deleted, along with its uploaded files.');
    }

    /**
     * Map validated input onto model attributes.
     *
     * @return array<string, mixed>
     */
    private function attributesFrom(StudyBuddyAppRequest $request, StudyBuddyMiniAppPlatform $app): array
    {
        $data = $request->safe()->except([
            'hero_image_file', 'image_url_file', 'remove_hero_image', 'remove_image_url',
            'web_app_zip', 'remove_web_app', 'learning_tags_text', 'learning_outcomes_text',
        ]);

        // A brand-new app defaults to every audience rather than none, which
        // would silently hide it from every role filter.
        $data['audience_roles'] = $request->input('audience_roles')
            ?: ($app->exists ? ($app->audience_roles ?: StudyBuddyAppRequest::ROLES) : StudyBuddyAppRequest::ROLES);

        $data['learning_tags'] = $this->splitList($request->input('learning_tags_text'), ',');
        $data['learning_outcomes'] = $this->splitList($request->input('learning_outcomes_text'), "\n");

        // The external-URL input stays visually empty for an uploaded build.
        // Preserve its publisher-managed internal address unless the admin is
        // removing it or replacing it with an external address. A replacement
        // ZIP keeps the old launch URL until publication succeeds.
        if (
            $app->exists
            && $app->usesUploadedBuild()
            && ! $request->filled('web_play_url')
            && ! $request->boolean('remove_web_app')
        ) {
            $data['web_play_url'] = $app->web_play_url;
        }

        // A new app with no explicit position goes to the end of the list
        // rather than colliding on 0 with everything else.
        if (! $request->filled('sort_order')) {
            $data['sort_order'] = $app->exists
                ? (int) $app->sort_order
                : (int) StudyBuddyMiniAppPlatform::max('sort_order') + 1;
        }

        // Keep the legacy free-text age_range column consistent with the
        // numeric range so older consumers keep reading a sensible value.
        if ($request->filled('age_min') || $request->filled('age_max')) {
            $min = $request->input('age_min');
            $max = $request->input('age_max');
            $data['age_range'] = $min && $max
                ? $min.'-'.$max
                : ($min ? $min.'+' : 'Up to '.$max);
        } else {
            $data['age_range'] = 'All ages';
        }

        $artworkSlug = ($data['slug'] ?? $app->slug) ?: 'app';
        $previousArtwork = array_values(array_filter([
            $app->hero_image,
            $app->image_url,
        ]));

        $data['hero_image'] = $this->resolveArtwork(
            $request->file('hero_image_file'),
            $request->boolean('remove_hero_image'),
            $request->input('hero_image'),
            $artworkSlug
        );

        $data['image_url'] = $this->resolveArtwork(
            $request->file('image_url_file'),
            $request->boolean('remove_image_url'),
            $request->input('image_url'),
            $artworkSlug
        );

        // Card and cover fields are allowed to share one uploaded file. Only
        // delete an old upload after neither final field references it.
        $nextArtwork = [$data['hero_image'], $data['image_url']];
        foreach (array_unique($previousArtwork) as $previousPath) {
            if (! in_array($previousPath, $nextArtwork, true)) {
                $this->deleteUploadedArtwork($previousPath);
            }
        }

        return $data;
    }

    /**
     * Decide the stored value for one artwork field: a new upload wins, then an
     * explicit removal, then whatever path the admin typed.
     */
    private function resolveArtwork(
        ?UploadedFile $upload,
        bool $remove,
        ?string $typedPath,
        string $slug
    ): ?string {
        if ($upload instanceof UploadedFile) {
            $stored = $upload->store(self::ARTWORK_DIRECTORY.'/'.$slug, 'public');

            // Store a root-relative path, never Storage::url()'s absolute URL:
            // an absolute URL bakes APP_URL into the row, so every image breaks
            // the moment the site is served from a different domain.
            $next = '/storage/'.$stored;
        } elseif ($remove) {
            $next = null;
        } else {
            $next = $typedPath !== null && trim($typedPath) !== '' ? trim($typedPath) : null;
        }

        return $next;
    }

    /**
     * Remove an uploaded file, but only if StudyBuddy is the one that stored it.
     * Typed-in paths and remote URLs are left alone.
     */
    private function deleteUploadedArtwork(?string $path): void
    {
        if (! $path) {
            return;
        }

        // Uploaded files are always stored as root-relative local paths. A
        // remote URL whose path happens to look similar never owns a local
        // file and must not be used as a deletion target.
        if (! str_starts_with($path, '/storage/'.self::ARTWORK_DIRECTORY.'/')) {
            return;
        }

        $relative = ltrim(rawurldecode((string) parse_url($path, PHP_URL_PATH)), '/');

        if (
            ! str_starts_with($relative, 'storage/'.self::ARTWORK_DIRECTORY.'/')
            || str_contains($relative, "\0")
            || preg_match('#(^|/)\.\.(/|$)#', str_replace('\\', '/', $relative))
        ) {
            return;
        }

        $disk = Storage::disk('public');
        $storedPath = substr($relative, strlen('storage/'));

        if ($disk->exists($storedPath) && ! $disk->delete($storedPath)) {
            throw new \RuntimeException('StudyBuddy could not remove an uploaded artwork file.');
        }

        $directory = dirname($storedPath);

        if (
            $directory !== '.'
            && $disk->allFiles($directory) === []
            && $disk->allDirectories($directory) === []
        ) {
            $disk->deleteDirectory($directory);
        }
    }

    /**
     * Apply the ZIP upload / removal for an app that is already saved.
     */
    private function syncWebBuild(
        StudyBuddyAppRequest $request,
        StudyBuddyMiniAppPlatform $app,
        StudyBuddyWebAppPublisher $publisher,
        ?StudyBuddyMiniAppPlatform $previousBrowserApp = null
    ): void {
        $remove = $request->boolean('remove_web_app');
        $upload = $request->file('web_app_zip');
        $externalUrl = trim((string) $request->input('web_play_url'));
        $switchingToExternal = $previousBrowserApp
            && preg_match('/^https:\/\//i', $externalUrl);

        if ($remove && $upload) {
            throw ValidationException::withMessages([
                'web_app_zip' => 'Choose either "remove the current web build" or upload a replacement ZIP, not both.',
            ]);
        }

        if ($remove) {
            $publisher->remove($previousBrowserApp ?? $app);
            $app->update([
                'web_play_url' => null,
                'web_app_package_path' => null,
                'web_app_entry_path' => null,
                'web_app_uploaded_at' => null,
                'is_web_enabled' => false,
            ]);

            return;
        }

        if ($switchingToExternal) {
            // The external address has already been saved. Remove the managed
            // ZIP/build it replaces, then clear only the managed metadata.
            $publisher->remove($previousBrowserApp);
            $app->update([
                'web_app_package_path' => null,
                'web_app_entry_path' => null,
                'web_app_uploaded_at' => null,
            ]);

            return;
        }

        if (! $upload) {
            return;
        }

        $previousPackage = $app->web_app_package_path;

        try {
            $published = $publisher->publish($app, $upload);
        } catch (\Throwable $exception) {
            // On edits, the other validated details have already been saved.
            // Say so plainly instead of implying the whole update disappeared.
            if (! $exception instanceof \RuntimeException) {
                report($exception);
            }

            $reason = $exception instanceof \RuntimeException
                ? $exception->getMessage()
                : 'The server could not safely process this ZIP. Try the file again or contact support.';

            throw ValidationException::withMessages([
                'web_app_zip' => 'The app details were saved, but the browser build was not replaced. '.$reason,
            ]);
        }

        $app->update(array_merge($published, [
            'is_web_enabled' => true,
            'status' => in_array($app->status, ['planned', 'concept'], true) ? 'beta' : $app->status,
        ]));

        $publisher->deleteStoredPackage($previousPackage);
    }

    /**
     * @return array<int, string>
     */
    private function splitList(?string $value, string $delimiter): array
    {
        return collect(explode($delimiter, (string) $value))
            ->map(fn ($item) => trim($item))
            ->filter()
            ->values()
            ->all();
    }

    /**
     * @return array<string, mixed>
     */
    private function formData(StudyBuddyMiniAppPlatform $app): array
    {
        return [
            'app' => $app,
            'statuses' => StudyBuddyAppRequest::STATUSES,
            'statusLabels' => StudyBuddyAppRequest::STATUS_LABELS,
            'roles' => [
                'student' => 'Learners',
                'parent' => 'Parents',
                'teacher' => 'Teachers',
                'independent_learner' => 'Independent learners',
            ],
            'categorySuggestions' => StudyBuddyMiniAppPlatform::query()
                ->whereNotNull('category')
                ->distinct()
                ->orderBy('category')
                ->pluck('category')
                ->filter()
                ->values(),
        ];
    }
}
