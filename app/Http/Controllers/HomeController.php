<?php

namespace App\Http\Controllers;

use App\Models\FooterItem;
use App\Models\HomepageSection;
use App\Models\NavigationItem;
use App\Models\SiteSetting;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home', [
            'settings' => SiteSetting::query()->pluck('value', 'key'),
            'navigationItems' => NavigationItem::query()->where('is_enabled', true)->orderBy('sort_order')->get(),
            'footerItems' => FooterItem::query()->where('is_enabled', true)->orderBy('group')->orderBy('sort_order')->get()->groupBy('group'),
            'sections' => HomepageSection::query()->with(['items' => fn ($q) => $q->where('is_enabled', true)->orderBy('sort_order')])->where('is_enabled', true)->orderBy('sort_order')->get(),
        ]);
    }
}
