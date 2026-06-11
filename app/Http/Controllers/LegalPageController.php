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
    public function contact(): View { return $this->show('contact'); }
    public function about(): View { return $this->show('about'); }

    private function show(string $key): View
    {
        return view('legal.show', [
            'page' => null,
            'sections' => collect(),
            'blocks' => collect(),
            'buttons' => collect(),
            'stats' => collect(),
            'cards' => collect(),
            'legalPage' => Cms::legal($key),
        ]);
    }
}
