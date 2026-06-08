<?php

namespace App\Http\Controllers;

use App\Support\Cms;
use Illuminate\View\View;

class AppController extends Controller
{
    public function index(): View
    {
        return view('apps.index', ['apps' => Cms::apps()]);
    }

    public function mathQuest(): View
    {
        return view('apps.math-quest', [
            'app' => Cms::apps()->firstWhere('slug', 'math-quest'),
        ]);
    }

    public function playMathQuest(): View
    {
        return view('apps.math-quest-play');
    }
}
