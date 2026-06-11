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
        return view('dashboards.shell', ['page' => DashboardPage::query()->where('key', $key)->first(), 'role' => 'parent']);
    }
}
