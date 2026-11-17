<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

/** Learners may self-report completion of their own verified class assignment. */
final class StudyBuddyAssignedWorkController extends Controller
{
    public function complete(Request $request, int $recipient): RedirectResponse
    {
        $user = $request->user();
        abort_unless($user && in_array($user->normalizedRole(), ['student', 'independent_learner'], true)
            && $user->hasVerifiedEmail() && !$user->is_admin, 403);

        foreach (['studybuddy_assignment_recipients', 'studybuddy_assignments',
            'studybuddy_learning_groups', 'studybuddy_group_members'] as $table) {
            abort_unless(Schema::hasTable($table), 404);
        }

        $row = DB::table('studybuddy_assignment_recipients as r')
            ->join('studybuddy_assignments as a', 'a.id', '=', 'r.assignment_id')
            ->join('studybuddy_learning_groups as g', function ($join) {
                $join->on('g.id', '=', 'a.group_id')->on('g.owner_id', '=', 'a.owner_id');
            })
            ->join('studybuddy_group_members as m', function ($join) {
                $join->on('m.group_id', '=', 'a.group_id')
                    ->on('m.user_id', '=', 'r.user_id')
                    ->on('m.owner_id', '=', 'a.owner_id');
            })
            ->where('r.id', $recipient)->where('r.user_id', $user->id)
            ->whereRaw('LOWER(r.email) = LOWER(?)', [$user->email])
            ->whereRaw('LOWER(m.email) = LOWER(r.email)')
            ->where('m.status', 'connected_with_code')
            ->where('m.member_role', 'student')
            ->where('g.type', 'class')->where('g.is_active', true)
            ->where('a.status', 'assigned')
            ->first(['r.id', 'r.status']);

        // Do not reveal whether another learner's assignment exists.
        abort_unless($row, 404);
        if ($row->status === 'completed') {
            return redirect()->route('dashboard')->with('status', 'This task was already marked complete.');
        }
        abort_unless($row->status === 'assigned', 404);

        DB::table('studybuddy_assignment_recipients')->where('id', $row->id)
            ->where('user_id', $user->id)->where('status', 'assigned')
            ->update(['status' => 'completed', 'completed_at' => now(), 'updated_at' => now()]);

        // Intentionally no points, grade, mastery flag, or teacher feedback.
        return redirect()->route('dashboard')
            ->with('status', 'Task marked complete. Your teacher can now see it in class progress.');
    }
}
