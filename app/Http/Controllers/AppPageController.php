<?php

namespace App\Http\Controllers;

use App\Models\MiniApp;
use App\Support\DemoContent;
use Illuminate\Support\Collection;
use Illuminate\View\View;

class AppPageController extends Controller
{
    public function index(): View
    {
        return view('apps.index', ['apps' => $this->apps()]);
    }

    public function show(string $app): View
    {
        $miniApp = $this->findApp($app);

        abort_if($miniApp === null, 404);

        return view($miniApp->slug === 'math-quest' ? 'apps.math-quest' : 'apps.show', [
            'app' => $miniApp,
        ]);
    }

    public function play(string $app): View
    {
        $miniApp = $this->findApp($app);

        abort_if($miniApp === null, 404);

        if (empty($miniApp->launch_path)) {
            return view('apps.coming-soon', ['app' => $miniApp]);
        }

        return view($miniApp->slug === 'math-quest' ? 'apps.math-quest-play' : 'apps.coming-soon', [
            'app' => $miniApp,
        ]);
    }

    private function apps(): Collection
    {
        if (class_exists(MiniApp::class)) {
            try {
                $apps = MiniApp::query()->orderBy('sort_order')->get();

                if ($apps->isNotEmpty()) {
                    return $apps;
                }
            } catch (\Throwable) {
                // Fall back to seeded demo content when the database is not migrated yet.
            }
        }

        return DemoContent::miniApps();
    }

    private function findApp(string $slug): ?object
    {
        return $this->apps()->firstWhere('slug', $slug);
    }
}
