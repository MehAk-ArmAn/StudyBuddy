<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use Illuminate\View\View;

class StudyBuddyControlRoomBridgeController extends Controller
{
    public function users(): View
    {
        return view('admin.control-room.module', [
            'title' => 'Users & Roles',
            'subtitle' => 'Manage students, parents, teachers, independent learners, and admin accounts.',
            'cards' => [
                ['title' => 'Users table', 'body' => 'Open the users resource if it exists in this build.', 'url' => $this->safeUrl('admin.users.index', '/admin/users')],
                ['title' => 'Create user', 'body' => 'Add or prepare a user account.', 'url' => $this->safeUrl('admin.users.create', '/admin/users/create')],
                ['title' => 'Control Room', 'body' => 'Return to the command center.', 'url' => url('/admin/control-room')],
            ],
        ]);
    }

    public function verifications(): View
    {
        return view('admin.control-room.module', [
            'title' => 'Safety Review',
            'subtitle' => 'Review parent, teacher, and learner safety workflows.',
            'cards' => [
                ['title' => 'Verification review', 'body' => 'Open the verification review module if available.', 'url' => $this->safeUrl('studybuddy.admin.verifications.index', '/admin/studybuddy/verifications')],
                ['title' => 'Website Shell', 'body' => 'Edit safety links shown in the public navbar/footer.', 'url' => url('/admin/control-room/shell')],
                ['title' => 'Control Room', 'body' => 'Return to the command center.', 'url' => url('/admin/control-room')],
            ],
        ]);
    }

    public function contentStudio(): View
    {
        return view('admin.control-room.module', [
            'title' => 'Content Studio',
            'subtitle' => 'Manage editable public content. This area routes through the current content studio module.',
            'cards' => [
                ['title' => 'Open Content Studio', 'body' => 'Edit public content blocks and pages.', 'url' => $this->safeUrl('studybuddy.admin.content-studio.index', '/admin/studybuddy/content-studio')],
                ['title' => 'Website Shell', 'body' => 'Edit navbar/footer content and links.', 'url' => url('/admin/control-room/shell')],
                ['title' => 'Control Room', 'body' => 'Return to the command center.', 'url' => url('/admin/control-room')],
            ],
        ]);
    }

    private function safeUrl(string $route, string $fallback): string
    {
        return Route::has($route) ? route($route) : url($fallback);
    }
}
