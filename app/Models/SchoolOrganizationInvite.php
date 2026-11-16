<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolOrganizationInvite extends Model
{
    protected $table = 'studybuddy_organization_invites';
    protected $guarded = ['id'];
    protected function casts(): array { return ['expires_at' => 'datetime', 'accepted_at' => 'datetime', 'revoked_at' => 'datetime']; }
    public function organization(): BelongsTo { return $this->belongsTo(SchoolOrganization::class, 'organization_id'); }
    public function inviter(): BelongsTo { return $this->belongsTo(User::class, 'invited_by'); }
}
