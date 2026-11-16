<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class StudyBuddyLaunchChecklistItem extends Model
{
    protected $table = 'studybuddy_launch_checklist_items';

    protected $fillable = [
        'area', 'title', 'description', 'status', 'priority', 'owner_label', 'sort_order',
    ];

    protected $casts = [
        'sort_order' => 'integer',
    ];
}
