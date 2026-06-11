<?php

namespace App\Http\Controllers;

use Illuminate\View\View;

class AdminResourceController extends Controller
{
    private const RESOURCES = [
        'apps' => 'Learning Apps',
        'rewards' => 'Rewards',
        'dashboard-cards' => 'Dashboard Cards',
        'site-content' => 'Site Content',
    ];

    public function index(string $resource): View
    {
        abort_unless(array_key_exists($resource, self::RESOURCES), 404);

        return view('admin.resources.index', [
            'resource' => $resource,
            'resourceTitle' => self::RESOURCES[$resource],
            'resources' => self::RESOURCES,
        ]);
    }
}
