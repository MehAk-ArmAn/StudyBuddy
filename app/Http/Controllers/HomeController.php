<?php

namespace App\Http\Controllers;

use App\Support\Cms;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('pages.home', [
            'featuredApps' => Cms::apps()->take(3),
        ]);
    }

    public function showcase(): View
    {
        return view('pages.showcase', [
            'contentBlocks' => collect(),
        ]);
    }
}
