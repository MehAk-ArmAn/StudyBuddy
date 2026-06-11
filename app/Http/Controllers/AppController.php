<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;

class AppController extends Controller
{
    public function index(): RedirectResponse
    {
        return redirect()->route('apps.index');
    }

    public function mathQuest(): RedirectResponse
    {
        return redirect()->route('apps.show', 'math-quest');
    }

    public function playMathQuest(): RedirectResponse
    {
        return redirect()->route('apps.play', 'math-quest');
    }
}
