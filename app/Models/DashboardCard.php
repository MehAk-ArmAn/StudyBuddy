<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class DashboardCard extends Model
{
    use HasFactory;

    protected $fillable = ['audience', 'title', 'metric', 'description', 'accent_color', 'sort_order'];

    protected function casts(): array
    {
        return ['sort_order' => 'integer'];
    }
}
