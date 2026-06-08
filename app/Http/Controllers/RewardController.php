<?php

namespace App\Http\Controllers;

use App\Support\Cms;
use Illuminate\View\View;

class RewardController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.rewards', ['rewards' => Cms::rewards()]);
    }
}
