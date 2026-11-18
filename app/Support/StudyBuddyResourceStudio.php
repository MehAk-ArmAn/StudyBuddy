<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Support\Str;

/** Account-owned resource metadata only; binary files stay on a private disk. */
final class StudyBuddyResourceStudio
{
    public const NOTE_LIMIT = 25;
    public const FILE_LIMIT = 30;
    public const FILE_BYTES_LIMIT = 62914560;

    public static function profile(User $user): array
    {
        $value = $user->role_profile;
        if (is_array($value)) return $value;
        $decoded = is_string($value) ? json_decode($value, true) : null;
        return is_array($decoded) ? $decoded : [];
    }

    public static function notes(User $user): array
    {
        return self::safeRecords(self::profile($user)['sb_p10_notes'] ?? null);
    }

    public static function files(User $user): array
    {
        return self::safeRecords(self::profile($user)['sb_p10_files'] ?? null);
    }

    private static function safeRecords(mixed $data): array
    {
        return array_values(array_filter(is_array($data) ? $data : [],
            fn ($item) => is_array($item) && is_string($item['id'] ?? null) && Str::isUuid($item['id'])
        ));
    }

    public static function find(array $records, string $id): ?array
    {
        if (!Str::isUuid($id)) return null;
        foreach ($records as $entry) {
            if (hash_equals((string) $entry['id'], $id)) return $entry;
        }
        return null;
    }

    public static function persist(User $user, string $key, array $records): void
    {
        // Fresh reads avoid overwriting other profile keys, including Packs 08 and 09.
        $user->refresh();
        $profile = self::profile($user);
        $profile[$key] = array_values($records);
        $user->role_profile = $profile;
        $user->save();
    }

    public static function storageDirectory(User $user): string
    {
        return 'studybuddy-p10-private/u'.$user->getKey();
    }

    public static function allowedMime(string $extension, string $mime): bool
    {
        $allowed = [
            'pdf' => ['application/pdf'],
            'txt' => ['text/plain'],
            'md' => ['text/plain', 'text/markdown'],
            'jpg' => ['image/jpeg'],
            'jpeg' => ['image/jpeg'],
            'png' => ['image/png'],
            'webp' => ['image/webp'],
            'mp3' => ['audio/mpeg', 'audio/mp3'],
            'wav' => ['audio/x-wav', 'audio/wav', 'audio/wave'],
            'ogg' => ['audio/ogg', 'application/ogg'],
        ];
        return in_array($mime, $allowed[$extension] ?? [], true);
    }

    public static function fileType(array $file): string
    {
        $mime = (string) ($file['mime'] ?? '');
        if (str_starts_with($mime, 'audio/') || $mime === 'application/ogg') return 'audio';
        if (str_starts_with($mime, 'image/')) return 'image';
        return 'document';
    }
}
