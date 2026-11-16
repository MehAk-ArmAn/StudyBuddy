<?php

namespace App\Http\Requests\Admin;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;

/**
 * Shared validation for creating and editing a StudyBuddy app.
 *
 * The same rules run for store() and update() so an app can never be created
 * in a state the edit screen would reject.
 */
class StudyBuddyAppRequest extends FormRequest
{
    public const STATUSES = ['concept', 'planned', 'beta', 'live', 'paused'];

    public const STATUS_LABELS = [
        'concept' => 'Early idea',
        'planned' => 'Planned',
        'beta' => 'Ready for testing',
        'live' => 'Ready to release',
        'paused' => 'Paused',
    ];

    public const ROLES = ['student', 'parent', 'teacher', 'independent_learner'];

    public function authorize(): bool
    {
        return (bool) $this->user()?->is_admin;
    }

    /**
     * Fill in the values an admin should not have to type by hand.
     */
    protected function prepareForValidation(): void
    {
        $name = trim((string) $this->input('name'));
        $slug = trim((string) $this->input('slug'));
        $androidUrl = trim((string) $this->input('android_url'));
        $androidPackageId = trim((string) $this->input('android_package_id'));

        // An empty slug is normal: most admins just type a name and save.
        if ($slug === '' && $name !== '') {
            $slug = $name;
        }

        // Google Play includes the immutable Android package name in its `id`
        // query parameter. Reading that value is simple URL parsing; no store
        // page is requested or scraped.
        if ($androidPackageId === '' && $androidUrl !== '') {
            parse_str((string) parse_url($androidUrl, PHP_URL_QUERY), $query);

            if (isset($query['id']) && is_string($query['id'])) {
                $androidPackageId = trim($query['id']);
            }
        }

        // "Save as draft" / "Save & publish" set visibility directly, so the
        // button an admin pressed always wins over the Published checkbox.
        $isActive = match ($this->input('save_action')) {
            'publish' => true,
            'draft' => false,
            default => $this->boolean('is_active'),
        };

        $this->merge([
            'name' => $name,
            'slug' => Str::slug($slug),
            'android_url' => $androidUrl !== '' ? $androidUrl : null,
            'android_package_id' => $androidPackageId !== '' ? $androidPackageId : null,
            // Unchecked checkboxes are absent from the payload, so absence has
            // to mean false rather than "leave the current value alone".
            'is_active' => $isActive,
            'is_featured' => $this->boolean('is_featured'),
            'is_web_enabled' => $this->boolean('is_web_enabled'),
            'is_download_enabled' => $this->boolean('is_download_enabled'),
        ]);
    }

    public function rules(): array
    {
        $appId = $this->route('app')?->id;
        $artworkLocation = function (string $attribute, mixed $value, \Closure $fail): void {
            $location = trim((string) $value);

            if ($location === '') {
                return;
            }

            if (str_starts_with($location, '/')) {
                $path = rawurldecode((string) parse_url($location, PHP_URL_PATH));

                if (
                    str_starts_with($location, '//')
                    || str_contains($path, "\0")
                    || str_contains($path, '\\')
                    || preg_match('#(^|/)\.\.(/|$)#', str_replace('\\', '/', $path))
                ) {
                    $fail('Use a safe image path without parent-directory segments.');
                }

                return;
            }

            if (! preg_match('#^https://#i', $location) || ! filter_var($location, FILTER_VALIDATE_URL)) {
                $fail('Use a root-relative image path or a full secure URL starting with https://');
            }
        };
        $secureUrl = function (string $attribute, mixed $value, \Closure $fail): void {
            $url = trim((string) $value);

            if ($url === '') {
                return;
            }

            if (
                strtolower((string) parse_url($url, PHP_URL_SCHEME)) !== 'https'
                || ! filter_var($url, FILTER_VALIDATE_URL)
            ) {
                $fail('Use a full secure URL starting with https://');
            }
        };

        return [
            'name' => ['required', 'string', 'max:160'],
            'slug' => [
                'required',
                'string',
                'max:160',
                'regex:/^[a-z0-9]+(?:-[a-z0-9]+)*$/',
                Rule::unique('studybuddy_mini_app_platforms', 'slug')->ignore($appId),
            ],
            'category' => ['required', 'string', 'max:120'],
            'status' => ['required', Rule::in(self::STATUSES)],
            'accent' => ['nullable', 'string', 'max:40'],
            'icon' => ['nullable', 'string', 'max:24'],

            'tagline' => ['nullable', 'string', 'max:240'],
            'description' => ['nullable', 'string', 'max:4000'],
            'long_description' => ['nullable', 'string', 'max:8000'],
            'preview_text' => ['nullable', 'string', 'max:1200'],
            'safety_note' => ['nullable', 'string', 'max:1200'],

            'hero_image' => ['nullable', 'string', 'max:500', $artworkLocation],
            'image_url' => ['nullable', 'string', 'max:500', $artworkLocation],
            'hero_image_file' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,gif', 'max:4096'],
            'image_url_file' => ['nullable', 'image', 'mimes:png,jpg,jpeg,webp,gif', 'max:4096'],
            'remove_hero_image' => ['nullable', 'boolean'],
            'remove_image_url' => ['nullable', 'boolean'],

            'age_min' => ['nullable', 'integer', 'min:3', 'max:120'],
            'age_max' => [
                'nullable',
                'integer',
                'min:3',
                'max:120',
                Rule::when($this->filled('age_min'), ['gte:age_min']),
            ],
            'age_range' => ['nullable', 'string', 'max:40'],
            'audience_roles' => ['nullable', 'array'],
            'audience_roles.*' => [Rule::in(self::ROLES)],
            'learning_tags_text' => ['nullable', 'string', 'max:600'],
            'learning_outcomes_text' => ['nullable', 'string', 'max:2000'],

            // Either a full external address, or the root-relative path the ZIP
            // publisher writes back (/web-apps/<slug>/index.html). Anything
            // else would produce a launch button that goes nowhere.
            'web_play_url' => [
                'nullable',
                'string',
                'max:500',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $value = trim((string) $value);

                    if ($value === '') {
                        return;
                    }

                    if (str_starts_with($value, '/')) {
                        $expected = '/web-apps/'.Str::slug((string) $this->input('slug')).'/index.html';

                        if ($value !== $expected) {
                            $fail('Uploaded builds use their automatic launch address. Upload the ZIP again if that address needs repairing.');
                        }

                        return;
                    }

                    if (! preg_match('#^https://#i', $value) || ! filter_var($value, FILTER_VALIDATE_URL)) {
                        $fail('The launch address needs to be a full link starting with https://');
                    }
                },
            ],
            'ios_url' => ['nullable', 'url', 'max:500', $secureUrl],
            'android_url' => [
                'nullable',
                'url',
                'max:500',
                function (string $attribute, mixed $value, \Closure $fail): void {
                    $url = trim((string) $value);
                    $scheme = strtolower((string) parse_url($url, PHP_URL_SCHEME));
                    $host = strtolower((string) parse_url($url, PHP_URL_HOST));
                    $path = rtrim((string) parse_url($url, PHP_URL_PATH), '/');
                    parse_str((string) parse_url($url, PHP_URL_QUERY), $query);
                    $packageId = $query['id'] ?? null;

                    if (
                        $scheme !== 'https'
                        || $host !== 'play.google.com'
                        || $path !== '/store/apps/details'
                    ) {
                        $fail('Paste the full Google Play listing link, starting with https://play.google.com/store/apps/details');
                    } elseif (! is_string($packageId) || trim($packageId) === '') {
                        $fail('The Google Play link needs to include the app package after “?id=”.');
                    }
                },
            ],
            'android_package_id' => [
                'nullable',
                'string',
                'max:255',
                'regex:/^[A-Za-z][A-Za-z0-9_]*(?:\.[A-Za-z][A-Za-z0-9_]*)+$/',
                Rule::unique('studybuddy_mini_app_platforms', 'android_package_id')->ignore($appId),
            ],
            'windows_url' => ['nullable', 'url', 'max:500', $secureUrl],
            'mac_url' => ['nullable', 'url', 'max:500', $secureUrl],
            'support_url' => ['nullable', 'url', 'max:500', $secureUrl],

            'points_reward' => ['required', 'integer', 'min:0', 'max:500'],
            'estimated_minutes' => ['required', 'integer', 'min:1', 'max:240'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:9999'],

            'is_active' => ['boolean'],
            'is_featured' => ['boolean'],
            'is_web_enabled' => ['boolean'],
            'is_download_enabled' => ['boolean'],

            'web_app_zip' => ['nullable', 'file', 'mimes:zip', 'max:61440'],
            'remove_web_app' => ['nullable', 'boolean'],
        ];
    }

    /**
     * Plain-language messages. Admins should never see a regex in an error.
     */
    public function messages(): array
    {
        return [
            'name.required' => 'Give the app a name so it can be listed.',
            'slug.required' => 'The web address needs a slug. Type an app name and we will suggest one.',
            'slug.unique' => 'Another app already uses that web address. Try a different slug.',
            'slug.regex' => 'Use lowercase letters, numbers and hyphens only, for example "math-quest".',
            'category.required' => 'Pick a category so the app shows up under the right filter.',
            'age_max.gte' => 'The maximum age needs to be the same as or higher than the minimum age.',
            'points_reward.required' => 'Set how many points a finished session is worth (0 is fine).',
            'estimated_minutes.required' => 'Set roughly how many minutes one session takes.',
            'hero_image_file.image' => 'The cover image needs to be a PNG, JPG, WEBP or GIF file.',
            'hero_image_file.max' => 'Keep the cover image under 4 MB.',
            'image_url_file.image' => 'The card image needs to be a PNG, JPG, WEBP or GIF file.',
            'image_url_file.max' => 'Keep the card image under 4 MB.',
            'web_app_zip.mimes' => 'The web build needs to be a .zip file.',
            'web_app_zip.max' => 'Keep the web build ZIP under 60 MB.',
            'ios_url.url' => 'The App Store link needs to be a full URL starting with https://',
            'android_url.url' => 'The Google Play link needs to be a full URL starting with https://',
            'android_package_id.regex' => 'Use a valid Android package ID, for example "com.example.learningapp".',
            'android_package_id.unique' => 'Another app already uses this Android package ID.',
            'windows_url.url' => 'The Windows link needs to be a full URL starting with https://',
            'mac_url.url' => 'The Mac link needs to be a full URL starting with https://',
            'support_url.url' => 'The support link needs to be a full URL starting with https://',
        ];
    }

    public function attributes(): array
    {
        return [
            'points_reward' => 'points',
            'estimated_minutes' => 'session length',
            'hero_image_file' => 'cover image',
            'image_url_file' => 'card image',
            'web_app_zip' => 'web build ZIP',
            'learning_tags_text' => 'learning tags',
            'learning_outcomes_text' => 'learning outcomes',
            'android_package_id' => 'Android package ID',
        ];
    }

    /**
     * Catch mutually exclusive browser-build choices before the controller
     * saves any part of the app.
     */
    public function withValidator($validator): void
    {
        $validator->after(function ($validator): void {
            $upload = $this->file('web_app_zip');
            $remove = $this->boolean('remove_web_app');
            $launchUrl = trim((string) $this->input('web_play_url'));

            if ($upload && $remove) {
                $validator->errors()->add(
                    'web_app_zip',
                    'Choose either “remove the current browser version” or upload a replacement ZIP, not both.'
                );
            }

            if ($upload && preg_match('#^https?://#i', $launchUrl)) {
                $validator->errors()->add(
                    'web_app_zip',
                    'Choose one browser option: upload a ZIP or use an external launch address.'
                );
            }

            $androidUrl = trim((string) $this->input('android_url'));
            $androidPackageId = trim((string) $this->input('android_package_id'));

            if ($androidUrl !== '' && $androidPackageId !== '') {
                parse_str((string) parse_url($androidUrl, PHP_URL_QUERY), $query);
                $urlPackageId = $query['id'] ?? null;

                if (
                    is_string($urlPackageId)
                    && trim($urlPackageId) !== ''
                    && trim($urlPackageId) !== $androidPackageId
                ) {
                    $validator->errors()->add(
                        'android_package_id',
                        'The Android package ID must match the “id” in the Google Play link.'
                    );
                }
            }
        });
    }
}
