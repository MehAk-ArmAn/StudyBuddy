<?php

namespace App\Http\Controllers;

use App\Support\DemoContent;
use Illuminate\View\View;

class DashboardController extends Controller
{
    public function student(): View
    {
        return $this->dashboard('primary', 'Student Mission Control');
    }

    public function studentApps(): View
    {
        return view('apps.index', ['apps' => DemoContent::miniApps()]);
    }

    public function studentRewards(): View
    {
        return view('pages.rewards', ['rewards' => DemoContent::rewards()]);
    }

    public function secondary(): View
    {
        return $this->dashboard('secondary', 'Secondary Scholar Flight Deck');
    }

    public function parent(): View
    {
        return $this->dashboard('parent', 'Parent Mission Control');
    }

    public function teacher(): View
    {
        return $this->dashboard('teacher', 'Teacher Orbit Studio');
    }

    public function admin(): View
    {
        return $this->dashboard('admin', 'Admin Galaxy Console');
    }

    private function dashboard(string $audience, string $title): View
    {
        return view('demo.dashboard', [
            'audience' => $audience,
            'title' => $title,
            'cards' => DemoContent::dashboardCards($audience),
        ]);
    }
}
