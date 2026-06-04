<?php

namespace App\Http\Controllers;

use App\Support\DemoContent;
use Illuminate\View\View;

class RewardController extends Controller
{
    public function __invoke(): View
    {
        return view('pages.rewards', ['rewards' => DemoContent::rewards()]);
    }
}
