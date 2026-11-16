<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageSection;
use App\Models\PageSectionItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageSectionItemController extends Controller
{
    private array $fields = [
        'item_key', 'title', 'subtitle', 'body', 'image_path', 'icon_path',
        'button_label', 'button_url', 'badge_text', 'sort_order', 'is_enabled', 'settings',
    ];

    public function index(Page $page, PageSection $section): View
    {
        return view('admin.resources.index', [
            'title' => 'Section Items: '.$section->title,
            'route' => 'admin.pages.sections.items',
            'fields' => $this->fields,
            'items' => $section->items()->orderBy('sort_order')->paginate(30),
            'parentParams' => [$page, $section],
        ]);
    }

    public function create(Page $page, PageSection $section): View
    {
        return view('admin.resources.form', [
            'title' => 'Create Section Item: '.$section->title,
            'route' => 'admin.pages.sections.items',
            'fields' => $this->fields,
            'item' => new PageSectionItem(),
            'method' => 'POST',
            'parentParams' => [$page, $section],
        ]);
    }

    public function store(Request $request, Page $page, PageSection $section): RedirectResponse
    {
        $section->items()->create($this->validated($request));
        return redirect()->route('admin.pages.sections.items.index', [$page, $section])->with('status', 'Saved.');
    }

    public function edit(Page $page, PageSection $section, PageSectionItem $item): View
    {
        return view('admin.resources.form', [
            'title' => 'Edit Section Item: '.$section->title,
            'route' => 'admin.pages.sections.items',
            'fields' => $this->fields,
            'item' => $item,
            'method' => 'PUT',
            'parentParams' => [$page, $section],
        ]);
    }

    public function update(Request $request, Page $page, PageSection $section, PageSectionItem $item): RedirectResponse
    {
        $item->update($this->validated($request));
        return redirect()->route('admin.pages.sections.items.index', [$page, $section])->with('status', 'Saved.');
    }

    public function destroy(Page $page, PageSection $section, PageSectionItem $item): RedirectResponse
    {
        $item->delete();
        return back()->with('status', 'Deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'item_key' => ['nullable', 'string', 'max:120'],
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'icon_path' => ['nullable', 'string', 'max:255'],
            'button_label' => ['nullable', 'string', 'max:120'],
            'button_url' => ['nullable', 'string', 'max:255'],
            'badge_text' => ['nullable', 'string', 'max:120'],
            'sort_order' => ['required', 'integer', 'min:0'],
            'is_enabled' => ['sometimes', 'boolean'],
            'settings' => ['nullable', 'json'],
        ]);

        $data['is_enabled'] = $request->boolean('is_enabled');
        if (isset($data['settings']) && is_string($data['settings'])) {
            $data['settings'] = json_decode($data['settings'], true);
        }

        return $data;
    }
}
