<?php

namespace Tests\Feature\Learning;

use App\Models\StudyBuddyMiniAppPlatform;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\DB;
use Tests\TestCase;

class StudyBuddyPack11FRewardsTest extends TestCase
{
    use RefreshDatabase;

    private function student(): User
    {
        return User::query()->create([
            'name'=>'Rewards QA', 'email'=>'reward-qa-'.uniqid().'@example.test',
            'password'=>'LocalTestPassword123!', 'role'=>'independent_learner',
        ]);
    }
    private function start(User $user, string $level='starter'): object
    {
        $this->actingAs($user)->post('/learning/math-sprint/start',['level'=>$level])
            ->assertRedirect(route('studybuddy.w32.index'));
        return DB::table('studybuddy_w32_math_runs')->where('user_id',$user->id)->latest('id')->firstOrFail();
    }
    private function finish(User $user, object $run, int $correct=5): void
    {
        $qs=json_decode($run->questions_json,true);
        $answers=[];
        foreach ($qs as $i=>$q) $answers[$i]=$i < $correct ? $q['answer'] : $q['answer']+100;
        $this->actingAs($user)->post('/learning/math-sprint/'.$run->id.'/submit',[
            'answers'=>$answers,'score'=>999,'points'=>999999,
        ])->assertRedirect(route('studybuddy.w32.result',$run->id));
    }
    public function test_verified_math_sprint_rewards_real_balance_and_ledger(): void
    {
        $u=$this->student();$old=(int)$u->fresh()->cosmic_points;
        $run=$this->start($u);
        DB::table('studybuddy_w32_math_runs')->where('id',$run->id)
            ->update(['started_at'=>now()->subSeconds(90)]);
        $this->finish($u,$run);
        $this->assertDatabaseHas('studybuddy_reward_events',[
            'user_id'=>$u->id,'event_key'=>'w32:run:'.$run->id,'score'=>5,'points_awarded'=>22,
        ]);
        $this->assertSame($old+22,(int)$u->fresh()->cosmic_points);
        $this->assertDatabaseHas('studybuddy_point_transactions',[
            'user_id'=>$u->id,'points'=>22,'source_type'=>'verified_math_sprint',
        ]);
        $this->assertDatabaseHas('studybuddy_user_reward_badges',['user_id'=>$u->id,'code'=>'math-perfect']);
        $this->assertContains('profile_badge:math-star',$u->fresh()->role_profile['unlocked_profile_items']);
        $this->actingAs($u)->get('/points-wallet')->assertOk()->assertSee('Perfect Numbers')->assertSee((string)($old+22));
    }
    public function test_very_fast_ungraded_or_wrong_sessions_cannot_mint_rewards(): void
    {
        $u=$this->student();$old=(int)$u->fresh()->cosmic_points;
        $fast=$this->start($u);$this->finish($u,$fast,5);
        $this->assertDatabaseCount('studybuddy_reward_events',0);
        $wrong=$this->start($u);
        DB::table('studybuddy_w32_math_runs')->where('id',$wrong->id)
            ->update(['started_at'=>now()->subSeconds(90)]);
        $this->finish($u,$wrong,2);
        $this->assertDatabaseCount('studybuddy_reward_events',0);
        $this->assertSame($old,(int)$u->fresh()->cosmic_points);
    }
    public function test_duplicate_completion_does_not_credit_twice(): void
    {
        $u=$this->student();$run=$this->start($u);
        DB::table('studybuddy_w32_math_runs')->where('id',$run->id)
            ->update(['started_at'=>now()->subSeconds(80)]);
        $this->finish($u,$run);
        $old=(int)$u->fresh()->cosmic_points;
        $this->finish($u,$run);
        $this->assertSame($old,(int)$u->fresh()->cosmic_points);
        $this->assertDatabaseCount('studybuddy_reward_events',1);
    }
    public function test_daily_cap_is_enforced_even_when_games_are_repeated(): void
    {
        $u=$this->student();$sum=0;
        for ($i=0;$i<4;$i++) {
            $run=$this->start($u);
            DB::table('studybuddy_w32_math_runs')->where('id',$run->id)
                ->update(['started_at'=>now()->subSeconds(90)]);
            $this->finish($u,$run);
        }
        $sum=(int) DB::table('studybuddy_reward_events')->where('user_id',$u->id)->sum('points_awarded');
        $this->assertSame(60,$sum);
    }
    public function test_hidden_profile_cosmetics_require_actual_badge_and_then_unlock(): void
    {
        $u=$this->student();
        $this->actingAs($u)->patch('/profile',[
            'name'=>$u->name,'email'=>$u->email,'profile_badge'=>'math-star',
        ])->assertSessionHasErrors('profile_badge');
        $run=$this->start($u);
        DB::table('studybuddy_w32_math_runs')->where('id',$run->id)
            ->update(['started_at'=>now()->subSeconds(85)]);
        $this->finish($u,$run);
        $this->actingAs($u)->patch('/profile',[
            'name'=>$u->name,'email'=>$u->email,'profile_badge'=>'math-star',
        ])->assertSessionHasNoErrors();
        $this->assertSame('math-star',$u->fresh()->role_profile['profile_badge']);
    }
    public function test_legacy_click_to_claim_is_disabled_for_real_and_unpublished_apps(): void
    {
        $u=$this->student();
        $this->actingAs($u)->postJson('/studybuddy/app-session/complete',['app_slug'=>'any-app'])
            ->assertStatus(409)->assertJsonPath('ok',false);
        $this->assertDatabaseCount('studybuddy_point_transactions',0);
        $this->assertDatabaseCount('studybuddy_reward_events',0);
    }
    public function test_non_head_cannot_open_governance_console(): void
    {
        $u=$this->student();
        $this->actingAs($u)->get('/admin/control-room/rewards')->assertRedirect();
    }
}
