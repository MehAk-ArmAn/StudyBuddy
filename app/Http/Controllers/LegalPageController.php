<?php

namespace App\Http\Controllers;

use App\Support\Cms;
use Illuminate\View\View;

class LegalPageController extends Controller
{
    public function privacy(): View { return $this->show('privacy-policy'); }
    public function terms(): View { return $this->show('terms-and-conditions'); }
    public function cookies(): View { return $this->show('cookie-policy'); }
    public function dataDeletion(): View { return $this->show('data-deletion'); }

    private function show(string $key): View
    {
        return view('public.legal', ['page' => Cms::legal($key)]);
    }
}
