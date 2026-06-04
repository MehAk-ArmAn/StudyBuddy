<?php

namespace App\Http\Controllers;

use App\Support\DemoContent;
use Illuminate\View\View;

class AppController extends Controller
{
    public function index(): View
    {
        return view('apps.index', ['apps' => DemoContent::miniApps()]);
    }

    public function mathQuest(): View
    {
        return view('apps.math-quest', [
            'app' => DemoContent::miniApps()->firstWhere('slug', 'math-quest'),
        ]);
    }

    public function playMathQuest(): View
    {
        return view('apps.math-quest-play');
    }
}
