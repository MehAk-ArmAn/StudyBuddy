<?php

namespace App\Http\Controllers;

use App\Support\DemoContent;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('pages.home', [
            'featuredApps' => DemoContent::miniApps()->take(3),
            'content' => DemoContent::siteContent('home')->keyBy('key'),
        ]);
    }

    public function showcase(): View
    {
        return view('pages.showcase', [
            'contentBlocks' => DemoContent::siteContent('showcase'),
        ]);
    }
}
