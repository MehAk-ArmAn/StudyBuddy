<?php

namespace App\Http\Controllers;

use App\Models\AccountConnection;
use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\ValidationException;

class AccountConnectionController extends Controller
{
    public function requestParentConnection(Request $request): RedirectResponse
    {
        $parent = $request->user();
        $this->ensureRole($parent, 'parent');
        $this->ensureAdultControls($parent, 'Parent controls require an adult parent account.');

        $data = $request->validate([
            'child_email' => ['required', 'email', 'exists:users,email'],
            'notes' => ['nullable', 'string', 'max:600'],
        ]);

        $child = User::query()->where('email', $data['child_email'])->firstOrFail();

        if ($child->id === $parent->id || $child->normalizedRole() !== 'student') {
            throw ValidationException::withMessages(['child_email' => 'Choose a real student account.']);
        }

        $this->upsertConnection($parent, $child, 'parent_child', [
            'view_progress' => true,
            'manage_routines' => true,
            'manage_safety' => true,
            'approve_teacher_links' => true,
            'no_password_access' => true,
            'no_private_message_access' => true,
            'requires_student_approval' => true,
        ], $data['notes'] ?? null);

        return back()->with('status', 'Parent request sent. The student must approve the connection before parent tools unlock.');
    }

    public function requestTeacherConnection(Request $request): RedirectResponse
    {
        $teacher = $request->user();
        $this->ensureRole($teacher, 'teacher');
        $this->ensureAdultControls($teacher, 'Teacher connections require an adult teacher account.');

        if ($teacher->role_verification_status !== 'verified' && ! $teacher->is_admin) {
            throw ValidationException::withMessages(['student_email' => 'Teacher supervision is locked until an admin verifies your teacher account.']);
        }

        $data = $request->validate([
            'student_email' => ['required', 'email', 'exists:users,email'],
            'notes' => ['nullable', 'string', 'max:600'],
        ]);

        $student = User::query()->where('email', $data['student_email'])->firstOrFail();

        if ($student->id === $teacher->id || $student->normalizedRole() !== 'student') {
            throw ValidationException::withMessages(['student_email' => 'Choose a real student account.']);
        }

        $this->upsertConnection($teacher, $student, 'teacher_student', [
            'view_limited_progress' => true,
            'assign_practice' => true,
            'classroom_notes' => true,
            'no_password_access' => true,
            'no_private_settings' => true,
            'limited_teacher_controls_only' => true,
            'requires_student_approval' => true,
        ], $data['notes'] ?? null);

        return back()->with('status', 'Teacher request sent. The student must approve before limited classroom tools unlock.');
    }

    public function approve(AccountConnection $connection): RedirectResponse
    {
        $this->ensureTargetCanAct($connection);

        $connection->update([
            'status' => 'approved',
            'approved_at' => now(),
            'rejected_at' => null,
        ]);

        if ($connection->type === 'parent_child') {
            $connection->requester->update([
                'role_verification_status' => 'verified',
                'role_verified_at' => now(),
            ]);
        }

        return back()->with('status', 'Connection approved. Controls are now active according to that role.');
    }

    public function reject(AccountConnection $connection): RedirectResponse
    {
        $this->ensureTargetCanAct($connection);
        $connection->update(['status' => 'rejected', 'rejected_at' => now()]);
        return back()->with('status', 'Connection rejected.');
    }

    public function revoke(AccountConnection $connection): RedirectResponse
    {
        $userId = Auth::id();
        if ($connection->requester_id !== $userId && $connection->target_id !== $userId) {
            abort(403);
        }
        $connection->update(['status' => 'revoked']);
        return back()->with('status', 'Connection revoked.');
    }

    private function upsertConnection(User $requester, User $target, string $type, array $permissions, ?string $notes): void
    {
        AccountConnection::query()->updateOrCreate(
            ['requester_id' => $requester->id, 'target_id' => $target->id, 'type' => $type],
            [
                'status' => 'pending',
                'requested_by_role' => (string) $requester->role,
                'permissions' => $permissions,
                'notes' => $notes,
                'approved_at' => null,
                'rejected_at' => null,
            ]
        );
    }

    private function ensureTargetCanAct(AccountConnection $connection): void
    {
        if ($connection->target_id !== Auth::id()) {
            abort(403);
        }
    }

    private function ensureRole(User $user, string $role): void
    {
        if ($user->normalizedRole() !== $role) {
            abort(403);
        }
    }

    private function ensureAdultControls(User $user, string $message): void
    {
        if (! $user->canUseAdultControls()) {
            throw ValidationException::withMessages(['role' => $message]);
        }
    }
}
