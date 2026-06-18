<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class UserAccessController extends Controller
{
    public function showLogin(): View
    {
        return view('auth.login');
    }
}
