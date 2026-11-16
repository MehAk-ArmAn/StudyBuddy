<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class SchoolOrganization extends Model
{
    protected $table = 'studybuddy_organizations';
    protected $guarded = ['id'];
    protected function casts(): array { return ['reviewed_at' => 'datetime']; }
    public function owner(): BelongsTo { return $this->belongsTo(User::class, 'requested_by'); }
    public function reviewer(): BelongsTo { return $this->belongsTo(User::class, 'reviewed_by'); }
    public function members(): HasMany { return $this->hasMany(SchoolOrganizationMember::class, 'organization_id'); }
    public function invitations(): HasMany { return $this->hasMany(SchoolOrganizationInvite::class, 'organization_id'); }
}
