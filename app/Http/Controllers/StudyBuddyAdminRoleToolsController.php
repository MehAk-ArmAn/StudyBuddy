<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class StudyBuddyAdminRoleToolsController extends Controller
{
    public function index(): View
    {
        $count = fn (string $table) => Schema::hasTable($table) ? DB::table($table)->count() : 0;

        $usersByRole = collect();
        if (Schema::hasTable('users')) {
            $usersByRole = DB::table('users')
                ->selectRaw('role, COUNT(*) as total')
                ->groupBy('role')
                ->orderBy('role')
                ->get();
        }

        $groups = Schema::hasTable('studybuddy_learning_groups')
            ? DB::table('studybuddy_learning_groups')->latest('id')->limit(20)->get()
            : collect();

        $members = Schema::hasTable('studybuddy_group_members')
            ? DB::table('studybuddy_group_members')->latest('id')->limit(30)->get()
            : collect();

        $assignments = Schema::hasTable('studybuddy_assignments')
            ? DB::table('studybuddy_assignments')->latest('id')->limit(30)->get()
            : collect();

        return view('admin.role-tools.index', [
            'stats' => [
                'students' => Schema::hasTable('users') ? DB::table('users')->where('role', 'student')->count() : 0,
                'parents' => Schema::hasTable('users') ? DB::table('users')->where('role', 'parent')->count() : 0,
                'teachers' => Schema::hasTable('users') ? DB::table('users')->where('role', 'teacher')->count() : 0,
                'independent' => Schema::hasTable('users') ? DB::table('users')->where('role', 'independent_learner')->count() : 0,
                'groups' => $count('studybuddy_learning_groups'),
                'members' => $count('studybuddy_group_members'),
                'assignments' => $count('studybuddy_assignments'),
                'assignmentRecipients' => $count('studybuddy_assignment_recipients'),
            ],
            'usersByRole' => $usersByRole,
            'groups' => $groups,
            'members' => $members,
            'assignments' => $assignments,
        ]);
    }
}
