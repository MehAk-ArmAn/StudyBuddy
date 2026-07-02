<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    public function index(): View
    {
        $query = SiteSetting::query();

        if (Schema::hasColumn('site_settings', 'sort_order')) {
            $query->orderBy('sort_order');
        }

        $items = $query->orderBy('id')->paginate(30);
        $route = request()->is('admin/control-room*') ? 'admin.control-room.site-settings' : 'admin.site-settings';

        return view('admin.resources.index', [
            'title' => 'Site Settings',
            'route' => $route,
            'fields' => $this->fields(),
            'items' => $items,
            'description' => 'Advanced settings table. For navbar/footer, use Website Shell.',
            'quick_links' => [
                ['label' => 'Website Shell', 'url' => url('/admin/control-room/shell')],
                ['label' => 'Control Room', 'url' => url('/admin/control-room')],
                ['label' => 'Preview site', 'url' => url('/')],
            ],
        ]);
    }

    public function create(): View
    {
        $route = request()->is('admin/control-room*') ? 'admin.control-room.site-settings' : 'admin.site-settings';

        return view('admin.resources.form', [
            'title' => 'Create Site Setting',
            'route' => $route,
            'fields' => $this->fields(),
            'item' => new SiteSetting(),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        SiteSetting::create($this->validated($request));
        return redirect($this->indexUrl())->with('status', 'Saved.');
    }

    public function edit(SiteSetting $siteSetting): View
    {
        $route = request()->is('admin/control-room*') ? 'admin.control-room.site-settings' : 'admin.site-settings';

        return view('admin.resources.form', [
            'title' => 'Edit Site Setting',
            'route' => $route,
            'fields' => $this->fields(),
            'item' => $siteSetting,
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, SiteSetting $siteSetting): RedirectResponse
    {
        $siteSetting->update($this->validated($request));
        return redirect($this->indexUrl())->with('status', 'Saved.');
    }

    public function destroy(SiteSetting $siteSetting): RedirectResponse
    {
        $siteSetting->delete();
        return back()->with('status', 'Deleted.');
    }

    private function indexUrl(): string
    {
        return request()->is('admin/control-room*')
            ? url('/admin/control-room/site-settings')
            : route('admin.site-settings.index');
    }

    private function fields(): array
    {
        $fields = ['key', 'value'];

        foreach (['type', 'group', 'sort_order'] as $column) {
            if (Schema::hasColumn('site_settings', $column)) {
                $fields[] = $column;
            }
        }

        return $fields;
    }

    private function validated(Request $request): array
    {
        $rules = [
            'key' => ['required', 'string', 'max:120'],
            'value' => ['nullable', 'string'],
        ];

        if (Schema::hasColumn('site_settings', 'type')) {
            $rules['type'] = ['nullable', 'string', 'max:40'];
        }
        if (Schema::hasColumn('site_settings', 'group')) {
            $rules['group'] = ['nullable', 'string', 'max:80'];
        }
        if (Schema::hasColumn('site_settings', 'sort_order')) {
            $rules['sort_order'] = ['nullable', 'integer'];
        }

        $data = $request->validate($rules);

        if (Schema::hasColumn('site_settings', 'type')) $data['type'] = $data['type'] ?? 'text';
        if (Schema::hasColumn('site_settings', 'group')) $data['group'] = $data['group'] ?? 'general';
        if (Schema::hasColumn('site_settings', 'sort_order')) $data['sort_order'] = $data['sort_order'] ?? 0;

        return $data;
    }
}
