<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class DashboardContentController extends Controller
{
    public function dashboards(): View
    {
        return view('admin.modules.index', ['module' => 'dashboards', 'table' => 'dashboard_pages']);
    }
}
