<?php

namespace App\Models;

use App\Services\StudyBuddyWebAppPublisher;
use Illuminate\Database\Eloquent\Model;

class StudyBuddyMiniAppPlatform extends Model
{
    protected $table = 'studybuddy_mini_app_platforms';

    protected $fillable = [
        // `long_description`, `image_url` and `age_range` were missing here, so the
        // admin form appeared to save them while mass assignment silently
        // dropped every value. They are admin-only content columns written
        // through a validated FormRequest.
        'long_description', 'image_url', 'age_range',
        'slug', 'name', 'category', 'tagline', 'description', 'status', 'icon', 'accent', 'hero_image',
        'preview_text', 'safety_note', 'web_play_url', 'web_app_package_path', 'web_app_entry_path', 'web_app_uploaded_at', 'ios_url', 'android_url', 'android_package_id', 'windows_url', 'mac_url', 'support_url',
        'points_reward', 'estimated_minutes', 'age_min', 'age_max', 'audience_roles', 'learning_tags',
        'learning_outcomes', 'detail_sections', 'is_web_enabled', 'is_download_enabled', 'is_featured', 'is_active', 'sort_order',
    ];

    protected $casts = [
        'audience_roles' => 'array',
        'learning_tags' => 'array',
        'learning_outcomes' => 'array',
        'detail_sections' => 'array',
        'web_app_uploaded_at' => 'datetime',
        'is_web_enabled' => 'boolean',
        'is_download_enabled' => 'boolean',
        'is_featured' => 'boolean',
        'is_active' => 'boolean',
        'points_reward' => 'integer',
        'estimated_minutes' => 'integer',
        'age_min' => 'integer',
        'age_max' => 'integer',
        'sort_order' => 'integer',
    ];

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderByDesc('is_featured')->orderBy('sort_order')->orderBy('name');
    }

    public function getAvailablePlatformsAttribute(): array
    {
        $platforms = [];
        if ($this->is_web_enabled) {
            $platforms['web'] = $this->web_play_url ?: route('studybuddy.final.web-play', $this->slug);
        }
        foreach (['ios' => 'ios_url', 'android' => 'android_url', 'windows' => 'windows_url', 'mac' => 'mac_url'] as $key => $column) {
            if (! empty($this->{$column})) {
                $platforms[$key] = $this->{$column};
            }
        }
        return $platforms;
    }

    public function visibleForRole(?string $role): bool
    {
        $roles = $this->audience_roles ?: ['student', 'parent', 'teacher', 'independent_learner'];
        if ($role === null) {
            return true;
        }
        return in_array($role, $roles, true);
    }


    public function hasPublishedWebApp(): bool
    {
        if (! $this->is_web_enabled || empty($this->web_play_url)) {
            return false;
        }

        if (preg_match('/^https?:\/\//i', $this->web_play_url)) {
            return true;
        }

        $directory = StudyBuddyWebAppPublisher::buildDirectory(
            (string) ($this->slug ?: $this->name)
        );

        return ! is_link(StudyBuddyWebAppPublisher::buildRoot())
            && ! is_link($directory)
            && file_exists($directory.DIRECTORY_SEPARATOR.'index.html');
    }

    public function launcherUrl(): ?string
    {
        if (! $this->hasPublishedWebApp()) {
            return null;
        }

        return route('studybuddy.final.web-play', $this->slug);
    }

    /**
     * True when the browser build is a ZIP published through StudyBuddy and
     * therefore served from our own domain.
     */
    public function usesUploadedBuild(): bool
    {
        return filled($this->web_app_entry_path)
            && ! preg_match('/^https?:\/\//i', (string) $this->web_play_url);
    }

    /**
     * True when the browser version points at a trusted site we do not host.
     */
    public function usesExternalBrowserUrl(): bool
    {
        return (bool) preg_match('/^https?:\/\//i', (string) $this->web_play_url);
    }

    /**
     * Where "Play in browser" should send someone, or null when there is no
     * browser version. Both kinds of build go through the StudyBuddy launcher
     * so the wrapper can offer a way back.
     */
    public function browserLaunchUrl(): ?string
    {
        return $this->launcherUrl();
    }

    /**
     * The store links an admin has actually filled in.
     *
     * Only configured platforms come back, so the public pages can never
     * render a button that goes nowhere.
     *
     * @return array<int, array{key:string, label:string, url:string}>
     */
    public function storeLinks(): array
    {
        if (! $this->is_download_enabled) {
            return [];
        }

        $catalogue = [
            'android' => ['Google Play', $this->android_url],
            'ios' => ['App Store', $this->ios_url],
            'windows' => ['Windows', $this->windows_url],
            'mac' => ['Mac', $this->mac_url],
        ];

        $links = [];

        foreach ($catalogue as $key => [$label, $url]) {
            if (filled($url)) {
                $links[] = ['key' => $key, 'label' => $label, 'url' => (string) $url];
            }
        }

        return $links;
    }

    /**
     * Every real way to get this app right now. An empty array means the app
     * is listed but not yet available anywhere, and the public pages say so
     * instead of showing dead buttons.
     *
     * @return array<int, array{key:string, label:string, url:string, primary:bool}>
     */
    public function availableActions(): array
    {
        $actions = [];

        if ($browser = $this->browserLaunchUrl()) {
            $actions[] = [
                'key' => 'browser',
                'label' => 'Play in browser',
                'url' => $browser,
                'primary' => true,
            ];
        }

        foreach ($this->storeLinks() as $link) {
            $actions[] = $link + ['primary' => false];
        }

        // With no browser build, the first store link becomes the main action.
        if ($actions !== [] && ! $actions[0]['primary']) {
            $actions[0]['primary'] = true;
        }

        return $actions;
    }

    /**
     * Whether a learner has at least one real destination for this app.
     * A store-only app is just as available as one with a browser build.
     */
    public function isAvailable(): bool
    {
        return $this->availableActions() !== [];
    }

    /**
     * The best available artwork for this app.
     *
     * `image_url` is where the original app art was stored, so it has to be
     * part of the fallback chain: reading `hero_image` alone made every public
     * app card fall through to the same generic placeholder.
     */
    public function cardImage(): ?string
    {
        return $this->image_url ?: ($this->hero_image ?: null);
    }

    public function detailImage(): ?string
    {
        return $this->hero_image ?: ($this->image_url ?: null);
    }

    /** Backwards-compatible name for older templates and integrations. */
    public function safeHeroImage(): ?string
    {
        return $this->detailImage();
    }

    /**
     * A stable accent colour pair for this app, used to draw a distinct tile
     * when no artwork file resolves. Derived from the slug so a given app keeps
     * the same colours on every page and every request.
     *
     * @return array{0:string, 1:string}
     */
    public function accentColors(): array
    {
        $palette = [
            ['#7c3cff', '#22d3ee'],
            ['#f472b6', '#fb923c'],
            ['#34d399', '#22d3ee'],
            ['#fbbf24', '#f472b6'],
            ['#60a5fa', '#a78bfa'],
            ['#f87171', '#fbbf24'],
            ['#2dd4bf', '#4ade80'],
            ['#a78bfa', '#f472b6'],
        ];

        $index = abs(crc32((string) ($this->slug ?: $this->name))) % count($palette);

        return $palette[$index];
    }

    /**
     * A short label to show inside the generated tile when there is no icon.
     */
    public function initials(): string
    {
        $words = preg_split('/[\s\-_]+/', (string) $this->name, -1, PREG_SPLIT_NO_EMPTY) ?: [];
        $letters = array_map(static fn (string $word): string => mb_strtoupper(mb_substr($word, 0, 1)), array_slice($words, 0, 2));

        return implode('', $letters) ?: 'SB';
    }

    /**
     * Public-facing availability label. Kept as a method (not an accessor) so it
     * cannot collide with a future `availability` column.
     */
    public function availabilityLabel(): string
    {
        if ($this->isAvailable()) {
            return 'Available now';
        }

        return match ($this->status) {
            'live' => 'Launching soon',
            'beta' => 'In testing',
            'planned' => 'On the way',
            'concept' => 'Being designed',
            'paused' => 'Paused',
            default => ucfirst((string) $this->status),
        };
    }
}
