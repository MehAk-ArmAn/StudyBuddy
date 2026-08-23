<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class StudyBuddyRoleDashboardActionController extends Controller
{
    public function regenerateConnectCode(Request $request): RedirectResponse
    {
        $user = $request->user();
        $profile = $this->profileArray($user->role_profile ?? null);
        $profile['connect_code'] = strtoupper(Str::random(8));

        DB::table('users')->where('id', $user->id)->update([
            'role_profile' => json_encode($profile, JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE),
            'updated_at' => now(),
        ]);

        return back()->with('status', 'Your StudyBuddy Connect Code was regenerated.');
    }

    public function addChild(Request $request): RedirectResponse
    {
        $user = $request->user();

        abort_unless(in_array($user->role, ['parent', 'admin'], true) || ($user->is_admin ?? false), 403);

        $data = $request->validate([
            'child_email' => ['required', 'email', 'max:190'],
            'child_connect_code' => ['required', 'string', 'min:4', 'max:20'],
        ], [
            'child_email.required' => 'Enter the email your child signed up with.',
            'child_email.email' => 'That does not look like an email address.',
            'child_connect_code.required' => 'Enter the Connect Code from your child\'s dashboard.',
            'child_connect_code.min' => 'Connect Codes are longer than that. Check it and try again.',
        ]);

        $childUser = $this->verifyLearnerConnection($data['child_email'], $data['child_connect_code'], 'child');

        $group = $this->ensureGroup($user->id, 'family', 'Family Learning Hub', null);

        DB::table('studybuddy_group_members')->updateOrInsert(
            ['group_id' => $group->id, 'email' => Str::lower($childUser->email)],
            [
                'owner_id' => $user->id,
                'user_id' => $childUser->id,
                'display_name' => $childUser->name,
                'member_role' => 'child',
                'status' => 'connected_with_code',
                'metrics_json' => json_encode(['verified_at' => now()->toDateTimeString(), 'method' => 'learner_connect_code']),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        if (Schema::hasColumn('users', 'child_emails')) {
            $emails = collect($user->child_emails ?? [])->push(Str::lower($childUser->email))->unique()->values()->all();
            DB::table('users')->where('id', $user->id)->update([
                'child_emails' => json_encode($emails),
                'updated_at' => now(),
            ]);
        }

        return back()->with('status', 'Child account connected with StudyBuddy Connect Code.');
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
        ], [
            'organization_name.required' => 'Add the name of your school or organisation.',
            'organization_email.email' => 'That does not look like an email address.',
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
        ], [
            'name.required' => 'Give the class a name, for example "Year 8 Maths".',
            'name.max' => 'That class name is too long. Keep it under 160 characters.',
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
            'student_email' => ['required', 'email', 'max:190'],
            'student_connect_code' => ['required', 'string', 'min:4', 'max:20'],
        ], [
            'student_email.required' => 'Enter the email the student signed up with.',
            'student_email.email' => 'That does not look like an email address.',
            'student_connect_code.required' => 'Enter the Connect Code from the student\'s dashboard.',
            'student_connect_code.min' => 'Connect Codes are longer than that. Check it and try again.',
        ]);

        $studentUser = $this->verifyLearnerConnection($data['student_email'], $data['student_connect_code'], 'student');

        DB::table('studybuddy_group_members')->updateOrInsert(
            ['group_id' => $class->id, 'email' => Str::lower($studentUser->email)],
            [
                'owner_id' => $user->id,
                'user_id' => $studentUser->id,
                'display_name' => $studentUser->name,
                'member_role' => 'student',
                'status' => 'connected_with_code',
                'metrics_json' => json_encode(['verified_at' => now()->toDateTimeString(), 'method' => 'learner_connect_code']),
                'updated_at' => now(),
                'created_at' => now(),
            ]
        );

        return back()->with('status', 'Student connected to class with StudyBuddy Connect Code.');
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
                ->where('status', 'connected_with_code')
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

        return back()->with('status', $groupId ? 'Assignment created and assigned to verified students.' : 'Assignment draft created.');
    }

    private function verifyLearnerConnection(string $email, string $code, string $label)
    {
        $email = Str::lower(trim($email));
        $code = strtoupper(trim($code));

        $learner = DB::table('users')->where('email', $email)->first();

        if (!$learner) {
            throw ValidationException::withMessages([
                "{$label}_email" => "This learner account does not exist yet. The learner must create an account first.",
            ]);
        }

        if (($learner->is_admin ?? false) || in_array($learner->role ?? '', ['parent', 'teacher', 'admin'], true)) {
            throw ValidationException::withMessages([
                "{$label}_email" => "Only student or independent learner accounts can be connected here.",
            ]);
        }

        $profile = $this->profileArray($learner->role_profile ?? null);
        $expected = strtoupper((string) ($profile['connect_code'] ?? ''));

        if (!$expected || !hash_equals($expected, $code)) {
            throw ValidationException::withMessages([
                "{$label}_connect_code" => "The StudyBuddy Connect Code is incorrect. Ask the learner to open their dashboard and share their current code.",
            ]);
        }

        return $learner;
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

    private function profileArray($value): array
    {
        if (is_array($value)) return $value;

        if (is_string($value) && trim($value) !== '') {
            $decoded = json_decode($value, true);
            return is_array($decoded) ? $decoded : [];
        }

        return [];
    }
}
