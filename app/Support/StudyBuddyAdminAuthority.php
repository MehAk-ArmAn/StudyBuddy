<?php

namespace App\Support;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * P11 staff authorization. Until the head is claimed, existing admin behavior
 * remains available for a controlled migration; production must claim head.
 */
final class StudyBuddyAdminAuthority
{
    public const HEAD_EMAIL = 'admin@studybuddy.fun';

    public const CAPABILITIES = [
        'platform.view' => 'Control room overview',
        'platform.users.manage' => 'Manage learner and test accounts',
        'schools.review' => 'Review and approve school applications',
        'apps.manage' => 'Manage learning apps and release catalog',
        'content.manage' => 'Manage website content and settings',
        'safety.review' => 'Review identity and safeguarding cases',
        'support.manage' => 'Manage support contact requests',
        'ops.view' => 'View limited operational information',
    ];

    public static function schemaReady(): bool
    {
        return Schema::hasTable('studybuddy_admin_grants');
    }

    public static function headId(): ?int
    {
        if (! self::schemaReady()) return null;
        $id = DB::table('studybuddy_admin_grants')
            ->where('kind', 'head')->where('is_active', true)->value('user_id');
        return $id === null ? null : (int) $id;
    }

    public static function isHead(?User $user): bool
    {
        return $user !== null && (bool) $user->is_admin
            && $user->role === 'admin'
            && self::headId() === (int) $user->id;
    }

    public static function can(?User $user, string $capability): bool
    {
        if (!$user || !$user->is_admin) return false;
        // Compatible with legacy admin fixtures until the deliberate claim step.
        if (self::headId() === null) return true;
        if ($user->role !== 'admin') return false;
        if (self::isHead($user)) return true;
        if (!self::schemaReady() || !$user->hasVerifiedEmail()) return false;
        $grant = DB::table('studybuddy_admin_grants')
            ->where('user_id', $user->id)->where('kind', 'staff')->where('is_active', true)->first();
        if (!$grant) return false;
        $permissions = json_decode($grant->capabilities ?: '[]', true);
        return is_array($permissions) && in_array($capability, $permissions, true);
    }

    public static function isProtected(?User $user): bool
    {
        return $user !== null && self::headId() !== null && self::headId() === (int) $user->id;
    }

    public static function requiredPermission(Request $request): ?string
    {
        $path = trim($request->path(), '/');
        if (!str_starts_with($path, 'admin/')) return null;

        if (preg_match('~^admin/(?:control-room/)?users(?:/|$)~', $path)) return 'platform.users.manage';
        if (str_starts_with($path, 'admin/control-room/team')) return 'platform.view';
        if (str_starts_with($path, 'admin/control-room/schools')) return 'schools.review';
        if (str_starts_with($path, 'admin/control-room/verifications') || str_starts_with($path, 'admin/studybuddy/verifications')) return 'safety.review';
        if (str_starts_with($path, 'admin/control-room/apps') || str_starts_with($path, 'admin/studybuddy/apps')) return 'apps.manage';
        if (str_starts_with($path, 'admin/control-room/messages') || str_starts_with($path, 'admin/control-room/contact-messages') || str_starts_with($path, 'admin/control-room/mailing-list')) return 'support.manage';
        if (str_starts_with($path, 'admin/control-room/health')) return 'ops.view';
        if (str_starts_with($path, 'admin/control-room/account')) return 'platform.view';
        if (str_starts_with($path, 'admin/control-room/role-tools')) return 'platform.view';
        if (str_starts_with($path, 'admin/studybuddy/shell') || str_starts_with($path, 'admin/studybuddy/content')) return 'content.manage';
        if (str_starts_with($path, 'admin/studybuddy/verifications')) return 'safety.review';
        if (str_starts_with($path, 'admin/studybuddy/apps')) return 'apps.manage';
        if ($path === 'admin/dashboard' || $path === 'admin/control-room') return 'platform.view';
        foreach (['/shell', '/content-studio', '/homepage-cms', '/pages-legal', '/pages', '/site-settings', '/media-assets', '/navigation-items', '/footer-items', '/homepage-sections'] as $fragment) {
            if (str_contains($path, $fragment)) return 'content.manage';
        }
        // Unknown or especially sensitive legacy routes are Head-only by default.
        return 'platform.head';
    }
}
