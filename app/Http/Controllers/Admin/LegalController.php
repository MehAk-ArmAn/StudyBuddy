<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class LegalController extends Controller
{
    public function legal(): View
    {
        return view('admin.modules.index', ['module' => 'legal', 'table' => 'legal_pages']);
    }
}
