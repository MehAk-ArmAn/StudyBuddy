<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SchoolOrganizationMember extends Model
{
    protected $table = 'studybuddy_organization_members';
    protected $guarded = ['id'];
    protected function casts(): array { return ['joined_at' => 'datetime']; }
    public function organization(): BelongsTo { return $this->belongsTo(SchoolOrganization::class, 'organization_id'); }
    public function user(): BelongsTo { return $this->belongsTo(User::class); }
}
