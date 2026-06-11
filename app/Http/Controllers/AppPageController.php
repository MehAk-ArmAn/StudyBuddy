<?php

namespace App\Http\Controllers;

use App\Support\Cms;
use Illuminate\View\View;

class AppPageController extends Controller
{
    public function index(): View
    {
        $page = Cms::page('apps');

        return view('public.apps.index', [
            'page' => $page,
            'sections' => Cms::sections($page),
            'apps' => Cms::miniApps(),
        ]);
    }

    public function show(string $app): View
    {
        return view('public.apps.show', ['app' => Cms::miniApp($app)]);
    }

    public function play(string $app): View
    {
        return view('public.apps.play', ['app' => Cms::miniApp($app)]);
    }
}
