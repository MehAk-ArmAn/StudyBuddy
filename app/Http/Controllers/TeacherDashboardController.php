<?php

namespace App\Http\Controllers;

use App\Models\DashboardPage;
use Illuminate\View\View;

class TeacherDashboardController extends Controller
{
    public function dashboard(): View { return $this->view('teacher.dashboard'); }
    public function classes(): View { return $this->view('teacher.classes'); }
    public function students(): View { return $this->view('teacher.students'); }
    public function assignments(): View { return $this->view('teacher.assignments'); }
    public function reports(): View { return $this->view('teacher.reports'); }
    public function settings(): View { return $this->view('teacher.settings'); }

    private function view(string $key): View
    {
        $page = DashboardPage::query()->where('key', $key)->first();

        return view('dashboards.teacher.shell', [
            'page' => $page,
            'sections' => collect(),
            'blocks' => collect(),
            'buttons' => collect(),
            'stats' => collect(),
            'cards' => collect(),
            'role' => 'teacher',
            'dashboardWidgets' => $page?->hasMany(\App\Models\DashboardWidget::class)->where('is_enabled', true)->orderBy('sort_order')->get() ?? collect(),
        ]);
    }
}
