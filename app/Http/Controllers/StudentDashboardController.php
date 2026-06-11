<?php

namespace App\Http\Controllers;

use App\Models\DashboardPage;
use Illuminate\View\View;

class StudentDashboardController extends Controller
{
    public function dashboard(): View { return $this->view('student.dashboard'); }
    public function apps(): View { return $this->view('student.apps'); }
    public function rewards(): View { return $this->view('student.rewards'); }
    public function progress(): View { return $this->view('student.progress'); }
    public function profile(): View { return $this->view('student.profile'); }
    public function settings(): View { return $this->view('student.settings'); }

    private function view(string $key): View
    {
        return view('dashboards.shell', ['page' => DashboardPage::query()->where('key', $key)->first(), 'role' => 'student']);
    }
}
