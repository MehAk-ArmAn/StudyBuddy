<?php

namespace App\Http\Controllers;

use App\Models\DashboardPage;
use Illuminate\View\View;

class ParentDashboardController extends Controller
{
    public function dashboard(): View { return $this->view('parent.dashboard'); }
    public function children(): View { return $this->view('parent.children'); }
    public function progress(): View { return $this->view('parent.progress'); }
    public function reports(): View { return $this->view('parent.reports'); }
    public function settings(): View { return $this->view('parent.settings'); }

    private function view(string $key): View
    {
        $page = DashboardPage::query()->where('key', $key)->first();

        return view('dashboards.parent.shell', [
            'page' => $page,
            'sections' => collect(),
            'blocks' => collect(),
            'buttons' => collect(),
            'stats' => collect(),
            'cards' => collect(),
            'role' => 'parent',
            'dashboardWidgets' => $page?->hasMany(\App\Models\DashboardWidget::class)->where('is_enabled', true)->orderBy('sort_order')->get() ?? collect(),
        ]);
    }
}
