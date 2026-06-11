<?php

namespace App\Http\Controllers;

use App\Support\Cms;
use Illuminate\View\View;

class AppPageController extends Controller
{
    public function index(): View
    {
        $page = Cms::page('apps');
        $sections = Cms::sections($page);

        return view('apps.index', [
            'page' => $page,
            'sections' => $sections,
            'blocks' => collect(),
            'buttons' => collect(),
            'stats' => Cms::stats($page),
            'cards' => collect(),
            'apps' => Cms::miniApps(),
        ]);
    }

    public function show(string $app): View
    {
        $appRecord = Cms::miniApp($app);

        return view('apps.show', [
            'page' => null,
            'sections' => collect(),
            'blocks' => collect(),
            'buttons' => collect(),
            'stats' => collect(),
            'cards' => collect(),
            'app' => $appRecord,
            'features' => Cms::appFeatures($appRecord),
        ]);
    }

    public function play(string $app): View
    {
        $appRecord = Cms::miniApp($app);

        return view('apps.play', [
            'page' => null,
            'sections' => collect(),
            'blocks' => collect(),
            'buttons' => collect(),
            'stats' => collect(),
            'cards' => collect(),
            'app' => $appRecord,
            'features' => Cms::appFeatures($appRecord),
        ]);
    }
}
