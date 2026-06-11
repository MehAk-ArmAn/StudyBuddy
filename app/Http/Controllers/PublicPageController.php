<?php

namespace App\Http\Controllers;

use App\Support\Cms;
use Illuminate\View\View;

class PublicPageController extends Controller
{
    public function home(): View
    {
        return $this->show('home');
    }

    public function rewards(): View
    {
        $page = Cms::page('rewards');
        $sections = Cms::sections($page);

        return view('public.rewards', [
            'page' => $page,
            'sections' => $sections,
            'blocks' => collect(),
            'buttons' => collect(),
            'stats' => Cms::stats($page),
            'cards' => collect(),
            'rewards' => Cms::rewards(),
        ]);
    }

    private function show(string $key): View
    {
        $page = Cms::page($key);
        $sections = Cms::sections($page);

        return view('public.page', [
            'page' => $page,
            'sections' => $sections,
            'blocks' => collect(),
            'buttons' => collect(),
            'stats' => Cms::stats($page),
            'cards' => collect(),
        ]);
    }
}
