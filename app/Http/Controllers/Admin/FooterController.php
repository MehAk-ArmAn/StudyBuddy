<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class FooterController extends Controller
{
    public function footer(): View
    {
        return view('admin.modules.index', ['module' => 'footer', 'table' => 'cms_footer_columns']);
    }
}
