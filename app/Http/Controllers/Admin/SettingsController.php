<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class SettingsController extends Controller
{
    public function settings(): View
    {
        return view('admin.modules.index', ['module' => 'settings', 'table' => 'site_settings']);
    }
}
