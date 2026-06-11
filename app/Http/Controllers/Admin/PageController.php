<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class PageController extends Controller
{
    public function pages(): View
    {
        return view('admin.modules.index', ['module' => 'pages', 'table' => 'cms_pages']);
    }
}
