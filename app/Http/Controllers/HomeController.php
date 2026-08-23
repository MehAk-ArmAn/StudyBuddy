<?php

namespace App\Http\Controllers;

use App\Models\FooterItem;
use App\Models\HomepageSection;
use App\Models\NavigationItem;
use App\Models\SiteSetting;
use App\Models\StudyBuddyMiniAppPlatform;
use Illuminate\View\View;

class HomeController extends Controller
{
    public function index(): View
    {
        return view('home', [
            // The homepage app strip comes from the real catalogue, so a newly
            // published app appears here with no template edit. When there is
            // nothing published the strip hides itself rather than showing
            // empty cards.
            'featuredApps' => StudyBuddyMiniAppPlatform::query()
                ->active()
                ->ordered()
                ->take(4)
                ->get(),
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
            'sections' => HomepageSection::query()
                ->with(['items' => fn ($query) => $query->where('is_enabled', true)->orderBy('sort_order')])
                ->where('is_enabled', true)
                ->orderBy('sort_order')
                ->get(),
        ]);
    }
}
