<?php

namespace App\Support;

use Illuminate\Support\Str;

/**
 * Turns database column names into words an administrator can read.
 *
 * The generic resource screens (Media, Navigation, Footer, Homepage Sections,
 * Site Settings, Pages) render straight from column lists, which is why they
 * used to show headings like "Is Active", "Mime Type" and "Sort Order".
 * Nobody should have to know the schema to use the admin.
 */
class AdminLabel
{
    /** Columns whose generated title case still reads like a database field. */
    private const OVERRIDES = [
        'is_active' => 'Status',
        'is_enabled' => 'Status',
        'is_published' => 'Status',
        'is_admin' => 'Admin',
        'is_featured' => 'Featured',
        'opens_new_tab' => 'Opens in new tab',
        'sort_order' => 'Order',
        'mime_type' => 'File type',
        'alt_text' => 'Description',
        'file_size' => 'Size',
        'image_path' => 'Image',
        'background_image_path' => 'Background image',
        'logo_path' => 'Logo',
        'favicon_path' => 'Favicon',
        'profile_photo_path' => 'Profile photo',
        'path' => 'File',
        'url' => 'Link',
        'button_url' => 'Button link',
        'button_label' => 'Button text',
        'secondary_button_url' => 'Second button link',
        'secondary_button_label' => 'Second button text',
        'section_key' => 'Section',
        'section_type' => 'Layout',
        'item_key' => 'Reference',
        'page_slug' => 'Page',
        'meta_title' => 'Search engine title',
        'meta_description' => 'Search engine description',
        'hero_title' => 'Headline',
        'hero_subtitle' => 'Sub-headline',
        'hero_body' => 'Intro text',
        'badge_text' => 'Badge',
        'eyebrow' => 'Small heading',
        'subtitle' => 'Sub-heading',
        'body' => 'Text',
        'settings' => 'Advanced settings',
        'extra' => 'Advanced settings',
        'key' => 'Name',
        'value' => 'Content',
        'group' => 'Section',
        'type' => 'Kind',
        'learning_stage' => 'Learning stage',
        'real_name' => 'Full name',
        'created_at' => 'Added',
        'updated_at' => 'Last edited',
    ];

    /** Columns that hold a yes/no flag. */
    private const BOOLEANS = [
        'is_active', 'is_enabled', 'is_published', 'is_admin',
        'is_featured', 'opens_new_tab', 'available_web',
    ];

    /** Columns that hold a path or URL to a picture. */
    private const IMAGES = [
        'path', 'image_path', 'background_image_path', 'logo_path',
        'favicon_path', 'hero_image', 'image_url', 'profile_photo_path',
    ];

    /** Columns worth hiding from a summary table: internal or usually empty. */
    private const TECHNICAL = [
        'settings', 'extra', 'metadata', 'meta', 'content_blocks',
        'role_profile', 'child_emails', 'settings_json', 'metrics_json',
    ];

    public static function humanize(string $field): string
    {
        return self::OVERRIDES[$field] ?? Str::of($field)
            ->replace('_', ' ')
            ->replace([' id', ' url'], [' ID', ' link'])
            ->ucfirst()
            ->toString();
    }

    public static function isBoolean(string $field): bool
    {
        return in_array($field, self::BOOLEANS, true);
    }

    public static function isImage(string $field): bool
    {
        return in_array($field, self::IMAGES, true);
    }

    public static function isTechnical(string $field): bool
    {
        return in_array($field, self::TECHNICAL, true);
    }

    /**
     * Wording for a yes/no flag, matched to what the column actually means.
     *
     * @return array{0:string, 1:bool} label and whether it reads as "on"
     */
    public static function booleanLabel(string $field, mixed $value): array
    {
        $on = filter_var($value, FILTER_VALIDATE_BOOLEAN);

        $label = match ($field) {
            'is_active', 'is_enabled', 'is_published' => $on ? 'Live' : 'Hidden',
            'is_admin' => $on ? 'Admin' : 'Member',
            'is_featured' => $on ? 'Featured' : '—',
            'opens_new_tab' => $on ? 'New tab' : 'Same tab',
            default => $on ? 'Yes' : 'No',
        };

        return [$label, $on];
    }

    /**
     * A friendly name for one row of a resource list, used in confirmations
     * so a delete prompt names the thing instead of saying "this item".
     */
    public static function describe(mixed $item): string
    {
        foreach (['title', 'name', 'label', 'key', 'section_key', 'slug'] as $field) {
            $value = data_get($item, $field);

            if (is_string($value) && trim($value) !== '') {
                return trim($value);
            }
        }

        return 'this item';
    }
}
