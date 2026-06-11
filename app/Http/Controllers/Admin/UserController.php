<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\View\View;

class UserController extends Controller
{
    public function users(): View
    {
        return view('admin.modules.index', ['module' => 'users', 'table' => 'users']);
    }
}
