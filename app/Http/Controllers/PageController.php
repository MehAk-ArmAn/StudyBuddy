<?php

namespace App\Http\Controllers;

use App\Models\FooterItem;
use App\Models\NavigationItem;
use App\Models\Page;
use App\Models\SiteSetting;
use Illuminate\View\View;

class PageController extends Controller
{
    public function show(string $slug): View
    {
        $page = Page::query()
            ->with(['sections' => fn ($query) => $query
                ->where('is_enabled', true)
                ->with(['items' => fn ($itemQuery) => $itemQuery->where('is_enabled', true)->orderBy('sort_order')])
                ->orderBy('sort_order')])
            ->where('slug', $slug)
            ->where('is_enabled', true)
            ->firstOrFail();

        return view('pages.show', [
            'page' => $page,
            'settings' => SiteSetting::query()->pluck('value', 'key')->toArray(),
            'navigationItems' => NavigationItem::query()
                ->where('is_enabled', true)
                ->orderBy('sort_order')
                ->get(),
            'footerGroups' => FooterItem::query()
                ->where('is_enabled', true)
                ->orderBy('group')
                ->orderBy('sort_order')
                ->get()
                ->groupBy('group'),
        ]);
    }
}
