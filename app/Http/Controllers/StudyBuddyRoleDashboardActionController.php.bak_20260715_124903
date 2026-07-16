<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class StudyBuddyRoleDashboardActionController extends Controller
{
    public function addChild(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless(in_array($user->role, ['parent', 'admin'], true) || ($user->is_admin ?? false), 403);

        $data = $request->validate([
            'child_name' => ['nullable', 'string', 'max:120'],
            'child_email' => ['required', 'email', 'max:190'],
        ]);

        $email = Str::lower(trim($data['child_email']));
        $group = $this->ensureGroup($user->id, 'family', 'Family Learning Hub', null);

        $childUser = Schema::hasTable('users')
            ? DB::table('users')->where('email', $email)->first()
            : null;

        DB::table('studybuddy_group_members')->updateOrInsert(
            ['group_id' => $group->id, 'email' => $email],
            [
                'owner_id' => $user->id,
                'user_id' => $childUser->id ?? null,
                'display_name' => $data['child_name'] ?: ($childUser->name ?? $email),
                'member_role' => 'child',
                'status' => $childUser ? 'connected' : 'invited',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        if (Schema::hasColumn('users', 'child_emails')) {
            $emails = collect($user->child_emails ?? [])->push($email)->unique()->values()->all();
            DB::table('users')->where('id', $user->id)->update([
                'child_emails' => json_encode($emails),
                'updated_at' => now(),
            ]);
        }

        return back()->with('status', 'Child account added to your parent dashboard.');
    }

    public function removeChild(Request $request, int $member): RedirectResponse
    {
        $user = $request->user();

        DB::table('studybuddy_group_members')
            ->where('id', $member)
            ->where('owner_id', $user->id)
            ->delete();

        return back()->with('status', 'Child connection removed from your dashboard.');
    }

    public function updateTeacherOrganization(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless(in_array($user->role, ['teacher', 'admin'], true) || ($user->is_admin ?? false), 403);

        $data = $request->validate([
            'organization_name' => ['required', 'string', 'max:190'],
            'organization_email' => ['nullable', 'email', 'max:190'],
            'position_title' => ['nullable', 'string', 'max:140'],
        ]);

        $payload = [];

        foreach ($data as $column => $value) {
            if (Schema::hasColumn('users', $column)) {
                $payload[$column] = $value;
            }
        }

        if ($payload) {
            $payload['updated_at'] = now();
            DB::table('users')->where('id', $user->id)->update($payload);
        }

        return back()->with('status', 'Teacher organization updated.');
    }

    public function createClass(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless(in_array($user->role, ['teacher', 'admin'], true) || ($user->is_admin ?? false), 403);

        $data = $request->validate([
            'name' => ['required', 'string', 'max:160'],
            'organization_name' => ['nullable', 'string', 'max:190'],
            'description' => ['nullable', 'string', 'max:500'],
        ]);

        DB::table('studybuddy_learning_groups')->insert([
            'owner_id' => $user->id,
            'type' => 'class',
            'name' => $data['name'],
            'organization_name' => $data['organization_name'] ?? ($user->organization_name ?? null),
            'invite_code' => strtoupper(Str::random(7)),
            'description' => $data['description'] ?? null,
            'settings' => json_encode(['created_from' => 'teacher_dashboard']),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return back()->with('status', 'Class created.');
    }

    public function addStudent(Request $request, int $group): RedirectResponse
    {
        $user = $request->user();

        $class = DB::table('studybuddy_learning_groups')
            ->where('id', $group)
            ->where('owner_id', $user->id)
            ->where('type', 'class')
            ->first();

        abort_unless($class, 404);

        $data = $request->validate([
            'student_name' => ['nullable', 'string', 'max:120'],
            'student_email' => ['required', 'email', 'max:190'],
        ]);

        $email = Str::lower(trim($data['student_email']));
        $studentUser = Schema::hasTable('users')
            ? DB::table('users')->where('email', $email)->first()
            : null;

        DB::table('studybuddy_group_members')->updateOrInsert(
            ['group_id' => $class->id, 'email' => $email],
            [
                'owner_id' => $user->id,
                'user_id' => $studentUser->id ?? null,
                'display_name' => $data['student_name'] ?: ($studentUser->name ?? $email),
                'member_role' => 'student',
                'status' => $studentUser ? 'connected' : 'invited',
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return back()->with('status', 'Student added to class roster.');
    }

    public function createAssignment(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless(in_array($user->role, ['teacher', 'admin'], true) || ($user->is_admin ?? false), 403);

        $data = $request->validate([
            'group_id' => ['nullable', 'integer'],
            'title' => ['required', 'string', 'max:180'],
            'type' => ['required', 'string', 'max:40'],
            'app_slug' => ['nullable', 'string', 'max:120'],
            'instructions' => ['nullable', 'string', 'max:2000'],
            'due_at' => ['nullable', 'date'],
            'points_reward' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'question_bank' => ['nullable', 'string', 'max:3000'],
        ]);

        $groupId = $data['group_id'] ?? null;

        if ($groupId) {
            $group = DB::table('studybuddy_learning_groups')
                ->where('id', $groupId)
                ->where('owner_id', $user->id)
                ->first();

            abort_unless($group, 404);
        }

        $assignmentId = DB::table('studybuddy_assignments')->insertGetId([
            'owner_id' => $user->id,
            'group_id' => $groupId,
            'title' => $data['title'],
            'type' => $data['type'],
            'app_slug' => $data['app_slug'] ?? null,
            'instructions' => $data['instructions'] ?? null,
            'due_at' => $data['due_at'] ?? null,
            'points_reward' => $data['points_reward'] ?? 50,
            'status' => $groupId ? 'assigned' : 'draft',
            'settings' => json_encode([
                'question_bank' => $data['question_bank'] ?? null,
                'created_from' => 'teacher_dashboard',
            ]),
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        if ($groupId) {
            $members = DB::table('studybuddy_group_members')
                ->where('group_id', $groupId)
                ->get();

            foreach ($members as $member) {
                DB::table('studybuddy_assignment_recipients')->insert([
                    'assignment_id' => $assignmentId,
                    'user_id' => $member->user_id,
                    'email' => $member->email,
                    'display_name' => $member->display_name,
                    'status' => 'assigned',
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

        return back()->with('status', $groupId ? 'Assignment created and assigned.' : 'Assignment draft created.');
    }

    private function ensureGroup(int $ownerId, string $type, string $name, ?string $organizationName)
    {
        $existing = DB::table('studybuddy_learning_groups')
            ->where('owner_id', $ownerId)
            ->where('type', $type)
            ->first();

        if ($existing) return $existing;

        $id = DB::table('studybuddy_learning_groups')->insertGetId([
            'owner_id' => $ownerId,
            'type' => $type,
            'name' => $name,
            'organization_name' => $organizationName,
            'invite_code' => strtoupper(Str::random(7)),
            'description' => null,
            'settings' => json_encode(['created_from' => 'role_dashboard']),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        return DB::table('studybuddy_learning_groups')->where('id', $id)->first();
    }
}
