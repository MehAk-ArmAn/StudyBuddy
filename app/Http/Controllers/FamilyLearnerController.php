<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

/**
 * Private parent-managed learner spaces for children of any age.
 * These are NOT child login accounts, and do not imply legal guardian verification.
 */
class FamilyLearnerController extends Controller
{
    public function index(Request $request): View
    {
        $parent = $this->parent($request);
        $family = $this->family($parent->id);
        $learners = $family ? DB::table('studybuddy_group_members')
            ->where('group_id', $family->id)
            ->where('owner_id', $parent->id)
            ->where('member_role', 'child')
            ->orderBy('id')
            ->get()
            ->map(function ($member) {
                $member->journey = $this->decode($member->metrics_json);
                $member->chapter = $this->chapter((int) ($member->journey['moments'] ?? 0));
                return $member;
            }) : collect();

        return view('family.learners', compact('learners'));
    }

    public function store(Request $request): RedirectResponse
    {
        $parent = $this->parent($request);
        $data = $request->validate([
            'display_name' => ['required', 'string', 'min:1', 'max:100'],
            'birth_year' => ['required', 'integer', 'between:1900,'.now()->year],
            'learning_goal' => ['nullable', 'string', 'max:190'],
            'favorite_subjects' => ['nullable', 'string', 'max:190'],
            'current_focus' => ['nullable', 'string', 'max:120'],
        ]);

        DB::transaction(function () use ($parent, $data): void {
            $family = $this->familyOrCreate($parent->id);
            DB::table('studybuddy_group_members')->insert([
                'group_id' => $family->id,
                'owner_id' => $parent->id,
                'user_id' => null,
                'email' => null, // No child's email required or published.
                'display_name' => trim($data['display_name']),
                'member_role' => 'child',
                'status' => 'family_managed',
                'metrics_json' => json_encode([
                    'version' => 1,
                    'birth_year' => (int) $data['birth_year'],
                    'learning_goal' => $data['learning_goal'] ?? '',
                    'favorite_subjects' => $data['favorite_subjects'] ?? '',
                    'current_focus' => $data['current_focus'] ?? '',
                    'moments' => 0,
                    'recent_moments' => [],
                ], JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        });

        return redirect()->route('studybuddy.family.learners')->with('status', 'Private family learning space created. This is not a child login account.');
    }

    public function update(Request $request, int $member): RedirectResponse
    {
        $parent = $this->parent($request);
        $data = $request->validate([
            'display_name' => ['required', 'string', 'min:1', 'max:100'],
            'learning_goal' => ['nullable', 'string', 'max:190'],
            'favorite_subjects' => ['nullable', 'string', 'max:190'],
            'current_focus' => ['nullable', 'string', 'max:120'],
        ]);
        DB::transaction(function () use ($parent, $member, $data): void {
            $row = $this->ownMember($parent->id, $member, true);
            $profile = $this->decode($row->metrics_json);
            foreach (['learning_goal', 'favorite_subjects', 'current_focus'] as $key) {
                $profile[$key] = $data[$key] ?? '';
            }
            DB::table('studybuddy_group_members')->where('id', $row->id)->update([
                'display_name' => trim($data['display_name']),
                'metrics_json' => json_encode($profile, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);
        });
        return back()->with('status', 'Learning preferences saved; all logged moments are retained.');
    }

    public function moment(Request $request, int $member): RedirectResponse
    {
        $parent = $this->parent($request);
        $data = $request->validate(['moment_note' => ['nullable', 'string', 'max:160']]);
        DB::transaction(function () use ($parent, $member, $data): void {
            $row = $this->ownMember($parent->id, $member, true);
            $profile = $this->decode($row->metrics_json);
            $profile['moments'] = min(100000, max(0, (int) ($profile['moments'] ?? 0)) + 1);
            $recent = array_values(array_slice((array) ($profile['recent_moments'] ?? []), -11));
            $recent[] = ['at' => now()->toDateString(), 'note' => trim($data['moment_note'] ?? '')];
            $profile['recent_moments'] = $recent;
            DB::table('studybuddy_group_members')->where('id', $row->id)->update([
                'metrics_json' => json_encode($profile, JSON_UNESCAPED_UNICODE | JSON_UNESCAPED_SLASHES),
                'updated_at' => now(),
            ]);
        });
        return back()->with('status', 'Learning moment logged. This is a family note, not a verified grade or reward.');
    }

    public function link(Request $request, int $member): RedirectResponse
    {
        $parent = $this->parent($request);
        // Linking to a separate real account is more sensitive than a private family note.
        abort_unless($parent->canUseAdultControls(), 403);
        // Reject cross-family member IDs before checking any target learner details.
        $this->ownMember($parent->id, $member);
        $data = $request->validate([
            'learner_email' => ['required', 'email:rfc', 'max:190'],
            'connect_code' => ['required', 'string', 'min:4', 'max:20'],
        ]);
        $learner = User::query()->where('email', Str::lower(trim($data['learner_email'])))->first();
        if (!$learner || !in_array($learner->normalizedRole(), ['student', 'independent_learner'], true)
            || !$learner->hasVerifiedEmail() || !$learner->date_of_birth || $learner->date_of_birth->age < 13) {
            throw ValidationException::withMessages(['learner_email' => 'Choose an email-verified learner account aged 13+ to connect.']);
        }
        $profile = $learner->role_profile ?? [];
        $expected = is_array($profile) ? ($profile['connect_code'] ?? '') : '';
        if (!$expected || !hash_equals(Str::upper(trim((string) $expected)), Str::upper(trim($data['connect_code'])))) {
            throw ValidationException::withMessages(['connect_code' => 'That learner Connect Code did not match.']);
        }
        $managed = $this->ownMember($parent->id, $member);
        $managedProfile = $this->decode($managed->metrics_json);
        $managedBirthYear = (int) ($managedProfile['birth_year'] ?? 0);
        if ($managedBirthYear && abs($managedBirthYear - (int) $learner->date_of_birth->year) > 1) {
            throw ValidationException::withMessages(['learner_email' => 'The birth year does not match this family profile. Check that you selected the right learner.']);
        }
        DB::transaction(function () use ($parent, $member, $learner): void {
            $row = $this->ownMember($parent->id, $member, true);
            abort_if($row->user_id !== null || $row->status !== 'family_managed', 409);
            DB::table('studybuddy_group_members')->where('id', $row->id)->update([
                'user_id' => $learner->id,
                'email' => $learner->email,
                'status' => 'connected_with_code',
                'updated_at' => now(),
            ]);
        });
        return back()->with('status', 'The existing learner account is linked. All family-logged learning moments are retained.');
    }

    public function destroy(Request $request, int $member): RedirectResponse
    {
        $parent = $this->parent($request);
        $row = $this->ownMember($parent->id, $member);
        // Do not silently unlink a live account. Existing parent connection controls handle that.
        abort_if($row->user_id !== null, 409);
        DB::table('studybuddy_group_members')->where('id', $row->id)->delete();
        return back()->with('status', 'Family-managed learner profile removed.');
    }

    private function parent(Request $request): User
    {
        $user = $request->user();
        abort_unless($user && $user->normalizedRole() === 'parent' && $user->isAdult() && $user->hasVerifiedEmail(), 403);
        abort_unless(Schema::hasTable('studybuddy_learning_groups') && Schema::hasTable('studybuddy_group_members'), 503);
        return $user;
    }

    private function family(int $ownerId): ?object
    {
        return DB::table('studybuddy_learning_groups')->where('owner_id', $ownerId)->where('type', 'family')->first();
    }

    private function familyOrCreate(int $ownerId): object
    {
        $existing = $this->family($ownerId);
        if ($existing) return $existing;
        $id = DB::table('studybuddy_learning_groups')->insertGetId([
            'owner_id' => $ownerId,
            'type' => 'family',
            'name' => 'Family Learning Hub',
            'organization_name' => null,
            'invite_code' => strtoupper(Str::random(8)),
            'description' => 'Private family-managed learning spaces',
            'settings' => json_encode(['created_from' => 'family_learners_w24']),
            'is_active' => true,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
        return DB::table('studybuddy_learning_groups')->where('id', $id)->first();
    }

    private function ownMember(int $ownerId, int $member, bool $lock = false): object
    {
        $q = DB::table('studybuddy_group_members as m')
            ->join('studybuddy_learning_groups as g', 'g.id', '=', 'm.group_id')
            ->where('m.id', $member)->where('m.owner_id', $ownerId)
            ->where('g.owner_id', $ownerId)->where('g.type', 'family')
            ->where('m.member_role', 'child')->select('m.*');
        if ($lock) $q->lockForUpdate();
        return $q->first() ?? abort(404);
    }

    private function decode(?string $json): array
    {
        $value = json_decode($json ?? '', true);
        return is_array($value) ? $value : [];
    }

    private function chapter(int $moments): string
    {
        return $moments >= 25 ? 'Creating a constellation' : ($moments >= 10 ? 'Finding your orbit' : ($moments >= 3 ? 'Building momentum' : 'Starting your journey'));
    }
}
