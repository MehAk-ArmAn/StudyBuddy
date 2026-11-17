<?php

namespace App\Support;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/**
 * Teacher-owned class assignment progress only. Deliberately does NOT expose a
 * learner's private activity journal, Math Sprint scores, reflections or badges.
 * A learner marking work finished is NOT proof of grading or skill mastery.
 */
final class StudyBuddyClassProgress
{
    public static function forTeacher($teacher): array
    {
        $result = ['state' => 'locked', 'classes' => collect(), 'totals' => [
            'classes' => 0, 'connected' => 0, 'assignments' => 0,
            'assigned' => 0, 'completed' => 0,
        ]];

        if (!$teacher || $teacher->normalizedRole() !== 'teacher'
            || !$teacher->hasVerifiedEmail() || !$teacher->canUseAdultControls()) {
            return $result;
        }
        foreach (['studybuddy_learning_groups', 'studybuddy_group_members',
            'studybuddy_assignments', 'studybuddy_assignment_recipients', 'users'] as $table) {
            if (!Schema::hasTable($table)) {
                $result['state'] = 'unavailable';
                return $result;
            }
        }

        $teacherId = (int) $teacher->id;
        $groups = DB::table('studybuddy_learning_groups')
            ->where('owner_id', $teacherId)->where('type', 'class')
            ->where('is_active', true)->orderBy('name')->orderBy('id')
            ->get(['id', 'name']);
        $result['state'] = 'ready';
        $result['totals']['classes'] = $groups->count();
        if ($groups->isEmpty()) {
            return $result;
        }
        $ids = $groups->pluck('id')->all();

        // A roster entry only counts if linked to a real learner through the
        // learner's Connect Code, with the same user ID and current email.
        $connected = DB::table('studybuddy_group_members as m')
            ->join('users as u', 'u.id', '=', 'm.user_id')
            ->where('m.owner_id', $teacherId)->whereIn('m.group_id', $ids)
            ->where('m.status', 'connected_with_code')->where('m.member_role', 'student')
            ->whereIn('u.role', ['student', 'independent_learner'])
            ->whereRaw('LOWER(m.email) = LOWER(u.email)')
            ->get(['m.group_id', 'm.user_id']);

        $assignments = DB::table('studybuddy_assignments')
            ->where('owner_id', $teacherId)->whereIn('group_id', $ids)
            ->where('status', 'assigned')->get(['id', 'group_id']);

        // Only the recipients of THIS teacher's actual class assignments, who
        // are STILL explicitly connected learners, contribute to the figures.
        $recipients = DB::table('studybuddy_assignment_recipients as r')
            ->join('studybuddy_assignments as a', 'a.id', '=', 'r.assignment_id')
            ->join('studybuddy_group_members as m', function ($join) {
                $join->on('m.group_id', '=', 'a.group_id')
                    ->on('m.user_id', '=', 'r.user_id')
                    ->on('m.owner_id', '=', 'a.owner_id');
            })
            ->join('users as u', 'u.id', '=', 'r.user_id')
            ->where('a.owner_id', $teacherId)->whereIn('a.group_id', $ids)
            ->where('a.status', 'assigned')
            ->where('m.status', 'connected_with_code')->where('m.member_role', 'student')
            ->whereIn('u.role', ['student', 'independent_learner'])
            ->whereRaw('LOWER(m.email) = LOWER(u.email)')
            ->whereRaw('LOWER(m.email) = LOWER(r.email)')
            ->get(['a.group_id', 'r.id', 'r.status', 'r.completed_at'])
            ->unique('id');

        $rows = collect();
        foreach ($groups as $group) {
            $classMembers = $connected->where('group_id', $group->id)->unique('user_id');
            $classAssignments = $assignments->where('group_id', $group->id);
            $classRecipients = $recipients->where('group_id', $group->id);
            $done = $classRecipients->filter(fn ($r) => $r->status === 'completed' && $r->completed_at !== null)->count();
            $assigned = $classRecipients->count();
            $row = [
                'name' => $group->name,
                'connected' => $classMembers->count(),
                'assignments' => $classAssignments->count(),
                'assigned' => $assigned,
                'completed' => $done,
                'pending' => $assigned - $done,
                'percent' => $assigned ? (int) round($done * 100 / $assigned) : 0,
            ];
            $rows->push($row);
            $result['totals']['connected'] += $row['connected'];
            $result['totals']['assignments'] += $row['assignments'];
            $result['totals']['assigned'] += $assigned;
            $result['totals']['completed'] += $done;
        }
        $result['classes'] = $rows;
        return $result;
    }
}
