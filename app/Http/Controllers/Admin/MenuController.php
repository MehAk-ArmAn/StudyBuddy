<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class MenuController extends Controller
{
    public function navigation(): View
    {
        return view('admin.modules.index', ['module' => 'navigation', 'table' => 'cms_menus']);
    }
}
