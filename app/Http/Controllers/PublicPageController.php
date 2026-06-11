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

    public function about(): View
    {
        return $this->show('about');
    }

    public function contact(): View
    {
        return $this->show('contact');
    }

    public function rewards(): View
    {
        return view('public.rewards', [
            'page' => Cms::page('rewards'),
            'sections' => Cms::sections(Cms::page('rewards')),
            'rewards' => Cms::rewards(),
        ]);
    }

    private function show(string $key): View
    {
        $page = Cms::page($key);

        return view('public.page', [
            'page' => $page,
            'sections' => Cms::sections($page),
            'stats' => Cms::stats($page),
        ]);
    }
}
