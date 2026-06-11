<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AppController extends Controller
{
    public function apps(): View
    {
        return view('admin.modules.index', ['module' => 'apps', 'table' => 'mini_apps']);
    }

    public function rewards(): View
    {
        return view('admin.modules.index', ['module' => 'rewards', 'table' => 'reward_items']);
    }
}
