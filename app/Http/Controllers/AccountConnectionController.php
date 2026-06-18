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
        $this->ensureEmailVerified($parent);
        $this->ensureAgeVerified($parent);

        $data = $request->validate([
            'child_email' => ['required', 'email', 'exists:users,email'],
            'notes' => ['nullable', 'string', 'max:600'],
        ]);

        $child = User::query()->where('email', $data['child_email'])->firstOrFail();

        if ($child->id === $parent->id || ! in_array($child->role, ['student', 'primary', 'secondary'], true)) {
            throw ValidationException::withMessages(['child_email' => 'Choose a real student account.']);
        }

        $this->upsertConnection($parent, $child, 'parent_child', [
            'view_progress' => true,
            'manage_routines' => true,
            'manage_safety' => true,
            'approve_teacher_links' => true,
            'full_child_controls_after_child_approval' => true,
        ], $data['notes'] ?? null);

        return back()->with('status', 'Parent request sent. The student must approve you before full controls unlock.');
    }

    public function requestTeacherConnection(Request $request): RedirectResponse
    {
        $teacher = $request->user();
        $this->ensureRole($teacher, 'teacher');
        $this->ensureEmailVerified($teacher);
        $this->ensureAgeVerified($teacher);

        if ($teacher->role_verification_status !== 'verified') {
            throw ValidationException::withMessages(['student_email' => 'Teacher supervision is locked until an admin verifies your teacher account.']);
        }

        $data = $request->validate([
            'student_email' => ['required', 'email', 'exists:users,email'],
            'notes' => ['nullable', 'string', 'max:600'],
        ]);

        $student = User::query()->where('email', $data['student_email'])->firstOrFail();

        if ($student->id === $teacher->id || ! in_array($student->role, ['student', 'primary', 'secondary'], true)) {
            throw ValidationException::withMessages(['student_email' => 'Choose a real student account.']);
        }

        $this->upsertConnection($teacher, $student, 'teacher_student', [
            'view_limited_progress' => true,
            'assign_practice' => true,
            'classroom_notes' => true,
            'no_password_or_private_settings' => true,
            'limited_teacher_controls_only' => true,
        ], $data['notes'] ?? null);

        return back()->with('status', 'Teacher request sent. The student must approve before limited classroom controls unlock.');
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

        $connection->update([
            'status' => 'rejected',
            'rejected_at' => now(),
        ]);

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
        if ($user->role !== $role) {
            abort(403);
        }
    }

    private function ensureEmailVerified(User $user): void
    {
        if (! $user->hasVerifiedEmail()) {
            throw ValidationException::withMessages(['email' => 'Verify your email before connecting accounts.']);
        }
    }

    private function ensureAgeVerified(User $user): void
    {
        if (! $user->age_verified_at) {
            throw ValidationException::withMessages(['date_of_birth' => 'Age verification is required for this role.']);
        }
    }
}
