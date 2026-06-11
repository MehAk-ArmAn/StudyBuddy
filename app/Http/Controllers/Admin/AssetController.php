<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class AssetController extends Controller
{
    public function assets(): View
    {
        return view('admin.modules.index', ['module' => 'assets', 'table' => 'asset_references']);
    }
}
