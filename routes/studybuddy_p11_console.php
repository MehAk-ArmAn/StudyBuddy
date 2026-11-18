<?php

use App\Models\StudyBuddySecurityEvent;
use App\Models\User;
use App\Support\StudyBuddyAdminAuthority as Authority;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

Artisan::command('studybuddy:p11:rotate-head-password {id}', function () {
    $user = User::findOrFail((int) $this->argument('id'));
    if (strcasecmp($user->email, Authority::HEAD_EMAIL) !== 0 || !$user->is_admin || $user->role !== 'admin') {
        $this->error('This is not the existing canonical administrator. No changes made.'); return 1;
    }
    $password = $this->secret('Enter a strong private password (12+ characters)');
    $repeat = $this->secret('Repeat the password');
    if (!is_string($password) || strlen($password) < 12 || $password !== $repeat
        || !preg_match('/[a-zA-Z]/', $password) || !preg_match('/\d/', $password)
        || in_array($password, ['ChangeMe12345!', 'REPLACE_WITH_YOUR_PRIVATE_PASSWORD', 'YOUR_NEW_STRONG_PASSWORD'], true)) {
        $this->error('Password not accepted; nothing changed.'); return 1;
    }
    $user->forceFill(['password' => Hash::make($password)])->save();
    StudyBuddySecurityEvent::record('p11_head_password_rotated', 'warning', $user->id, ['via'=>'console']);
    $this->info('Password updated. Do not put passwords in shell commands or Git.');
    return 0;
})->purpose('Privately rotate the existing canonical admin password without shell history');

Artisan::command('studybuddy:p11:claim-head {id}', function () {
    if (!Authority::schemaReady()) { $this->error('Run the P11 migration first.'); return 1; }
    $user = User::find((int) $this->argument('id'));
    if (!$user || strcasecmp($user->email, Authority::HEAD_EMAIL) !== 0
        || !$user->is_admin || $user->role !== 'admin') {
        $this->error('The supplied ID is not the existing canonical admin account.'); return 1;
    }
    foreach (['ChangeMe12345!', 'REPLACE_WITH_YOUR_PRIVATE_PASSWORD', 'YOUR_NEW_STRONG_PASSWORD'] as $bad) {
        if (Hash::check($bad, $user->password)) {
            $this->error('Rotate the insecure/placeholder password before claiming Head Admin.'); return 1;
        }
    }
    try {
        DB::transaction(function () use ($user) {
            $heads = DB::table('studybuddy_admin_grants')->where('kind', 'head')->lockForUpdate()->pluck('user_id');
            if ($heads->isNotEmpty() && !$heads->contains($user->id)) {
                throw new RuntimeException('Head already claimed for another user. No changes made.');
            }
            $existing = DB::table('studybuddy_admin_grants')->where('user_id', $user->id)->lockForUpdate()->first();
            if ($existing && $existing->kind !== 'head') throw new RuntimeException('Conflicting staff grant; resolve explicitly.');
            DB::table('studybuddy_admin_grants')->updateOrInsert(['user_id'=>$user->id], [
                'kind'=>'head', 'capabilities'=>json_encode(array_keys(Authority::CAPABILITIES)),
                'is_active'=>true, 'granted_by'=>$user->id, 'updated_at'=>now(), 'created_at'=>$existing?->created_at ?? now(),
            ]);
        });
    } catch (Throwable $error) {
        $this->error($error->getMessage()); return 1;
    }
    StudyBuddySecurityEvent::record('p11_head_claimed', 'critical', $user->id, ['user_id'=>$user->id]);
    $this->info('Protected Head Admin claimed for user ID '.$user->id.'. Existing legacy users were NOT deleted.');
    return 0;
})->purpose('Explicitly anchor Head Admin to an existing account ID; never deletes older users');

Artisan::command('studybuddy:p11:retire-legacy {id}', function () {
    $headId = Authority::headId();
    if (!$headId) { $this->error('Claim Head Admin first.'); return 1; }
    $legacy = User::find((int) $this->argument('id'));
    if (!$legacy || strcasecmp($legacy->email, 'admin@studybuddy.local') !== 0 || $legacy->id === $headId) {
        $this->error('Only the exact old .local account ID may be retired.'); return 1;
    }
    if (DB::table('studybuddy_admin_grants')->where('user_id', $legacy->id)->where('is_active', true)->exists()) {
        $this->error('This account has an active PCL team grant; revoke it separately.'); return 1;
    }
    $this->warn('This retains ALL school/data/audit rows but disables legacy platform admin login for user ID '.$legacy->id.'.');
    if (!$this->confirm('Have you backed up the database and verified the .fun Head Admin can log in?', false)) {
        $this->line('No changes made.'); return 1;
    }
    $legacy->forceFill(['is_admin'=>false, 'role'=>'independent_learner', 'remember_token'=>\Illuminate\Support\Str::random(60)])->save();
    StudyBuddySecurityEvent::record('p11_legacy_admin_retired', 'critical', $headId, ['legacy_user_id'=>$legacy->id]);
    $this->info('Legacy account retired without deleting the user or linked records.');
    return 0;
})->purpose('Optionally remove legacy .local platform access after safe Head Admin verification');
