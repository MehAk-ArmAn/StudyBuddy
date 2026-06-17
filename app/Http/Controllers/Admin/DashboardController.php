<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterItem; use App\Models\HomepageSection; use App\Models\MediaAsset; use App\Models\NavigationItem;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function __invoke(): View
    {
        return view('admin.dashboard', ['enabledSections'=>HomepageSection::where('is_enabled', true)->count(), 'navItems'=>NavigationItem::count(), 'footerItems'=>FooterItem::count(), 'mediaAssets'=>MediaAsset::count()]);
    }
}
