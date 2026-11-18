<?php

/**
 * STUDYBUDDY-TEAM-HANDOVER-2026
 * Developer orientation: Head Admin-only reward policy: validate both read/write authority on the server, audit any rule changes, and keep feature flags and caps enforced in the reward service.
 *
 * Documentation-only annotation; no runtime behavior is changed here.
 */


namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Support\StudyBuddyRewardIntegrity;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

final class StudyBuddyP11FRewardsController extends Controller
{
    private function head(Request $request): int
    {
        $id = (int)($request->user()?->id ?? 0);
        abort_unless(StudyBuddyRewardIntegrity::isHead($id), 403);
        return $id;
    }

    public function index(Request $request): View
    {
        $this->head($request);
        $settings = DB::table('studybuddy_reward_settings')->where('id',1)->first();
        $rules = DB::table('studybuddy_reward_rules')->orderBy('game_key')->get();
        $audits = DB::table('studybuddy_reward_rule_audits')->orderByDesc('id')->limit(25)->get();
        $stats = [
            'verified_completions'=>DB::table('studybuddy_reward_events')->count(),
            'minted_points'=>DB::table('studybuddy_reward_events')->sum('points_awarded'),
            'achievement_unlocks'=>DB::table('studybuddy_user_reward_badges')->count(),
        ];
        return view('admin.p11f.rewards',compact('settings','rules','audits','stats'));
    }

    public function updateSettings(Request $request): RedirectResponse
    {
        $actor = $this->head($request);
        $data = $request->validate([
            'enabled'=>['required',Rule::in(['0','1'])],
            'global_daily_cap'=>['required','integer','between:1,200'],
        ]);
        DB::transaction(function () use ($actor,$data): void {
            $old = DB::table('studybuddy_reward_settings')->where('id',1)->lockForUpdate()->first();
            $after = ['enabled'=>(bool)(int)$data['enabled'],'global_daily_cap'=>(int)$data['global_daily_cap']];
            DB::table('studybuddy_reward_settings')->where('id',1)->update($after+['updated_at'=>now()]);
            $this->audit($actor,'change_global_policy','global', (array)$old,$after);
        });
        return back()->with('status','Global reward limits updated and audited.');
    }

    public function updateRule(Request $request, int $rule): RedirectResponse
    {
        $actor = $this->head($request);
        $data = $request->validate([
            'enabled'=>['required',Rule::in(['0','1'])],
            'min_correct'=>['required','integer','between:3,5'],
            'min_seconds'=>['required','integer','between:10,180'],
            'points_per_correct'=>['required','integer','between:1,12'],
            'time_bonus_after_seconds'=>['required','integer','between:45,300'],
            'time_bonus_points'=>['required','integer','between:0,3'],
            'max_points_per_run'=>['required','integer','between:1,50'],
        ]);
        DB::transaction(function () use ($actor,$data,$rule): void {
            $old = DB::table('studybuddy_reward_rules')->where('id',$rule)->lockForUpdate()->first();
            abort_unless($old,404);
            $after = array_map('intval',$data);
            $after['enabled']=(bool)$after['enabled'];
            DB::table('studybuddy_reward_rules')->where('id',$rule)->update($after+['updated_at'=>now()]);
            $this->audit($actor,'change_game_rule',$old->game_key,(array)$old,$after);
        });
        return back()->with('status','Game reward rule saved and audited.');
    }

    private function audit(int $actor,string $action,string $target,array $before,array $after): void
    {
        unset($before['created_at'],$before['updated_at']);
        DB::table('studybuddy_reward_rule_audits')->insert([
            'actor_id'=>$actor,'action'=>$action,'target'=>$target,
            'before_value'=>json_encode($before),'after_value'=>json_encode($after),
            'created_at'=>now(),'updated_at'=>now(),
        ]);
    }
}
