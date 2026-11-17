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
        // One canonical, published Apps CMS collection powers the hero count and real game shelf.
        // Explicit demo concepts are previews, not published educational games.
        $catalogue = StudyBuddyMiniAppPlatform::query()->active()->ordered()->get()
            ->filter(fn (StudyBuddyMiniAppPlatform $app): bool =>
                ! str_starts_with((string) $app->slug, 'demo-next-')
                && $app->visibleForRole(auth()->user()?->role))
            ->values();

        $ageMin = $catalogue->pluck('age_min')->filter(fn ($age) => is_numeric($age) && $age > 0)->min();
        $ageMax = $catalogue->pluck('age_max')->filter(fn ($age) => is_numeric($age) && $age > 0)->max();
        $publicAgeRange = $ageMin !== null && $ageMax !== null && $ageMin <= $ageMax
            ? $ageMin.'–'.$ageMax : null;

        return view('home', [
            'publicAppCount' => $catalogue->count(),
            'publicAgeRange' => $publicAgeRange,
            // The homepage app strip comes from the real catalogue, so a newly
            // published app appears here with no template edit. When there is
            // nothing published the strip hides itself rather than showing
            // empty cards.
            'featuredApps' => $catalogue->take(4),
            'discoveryApps' => $catalogue->take(12),
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
