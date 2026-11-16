<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use App\Models\PageSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageSectionController extends Controller
{
    private array $fields = [
        'section_key', 'section_type', 'eyebrow', 'title', 'subtitle', 'body',
        'image_path', 'button_label', 'button_url', 'sort_order', 'is_enabled', 'settings',
    ];

    public function index(Page $page): View
    {
        return view('admin.resources.index', [
            'title' => 'Page Sections: '.$page->title,
            'route' => 'admin.pages.sections',
            'fields' => $this->fields,
            'items' => $page->sections()->orderBy('sort_order')->paginate(30),
            'parentParams' => [$page],
            'page' => $page,
        ]);
    }

    public function create(Page $page): View
    {
        return view('admin.resources.form', [
            'title' => 'Create Page Section: '.$page->title,
            'route' => 'admin.pages.sections',
            'fields' => $this->fields,
            'item' => new PageSection(),
            'method' => 'POST',
            'parentParams' => [$page],
        ]);
    }

    public function store(Request $request, Page $page): RedirectResponse
    {
        $page->sections()->create($this->validated($request));
        return redirect()->route('admin.pages.sections.index', $page)->with('status', 'Saved.');
    }

    public function edit(Page $page, PageSection $section): View
    {
        return view('admin.resources.form', [
            'title' => 'Edit Page Section: '.$page->title,
            'route' => 'admin.pages.sections',
            'fields' => $this->fields,
            'item' => $section,
            'method' => 'PUT',
            'parentParams' => [$page],
        ]);
    }

    public function update(Request $request, Page $page, PageSection $section): RedirectResponse
    {
        $section->update($this->validated($request));
        return redirect()->route('admin.pages.sections.index', $page)->with('status', 'Saved.');
    }

    public function destroy(Page $page, PageSection $section): RedirectResponse
    {
        $section->delete();
        return back()->with('status', 'Deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'section_key' => ['required', 'string', 'max:120'],
            'section_type' => ['required', 'string', 'max:80'],
            'eyebrow' => ['nullable', 'string', 'max:160'],
            'title' => ['nullable', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string'],
            'body' => ['nullable', 'string'],
            'image_path' => ['nullable', 'string', 'max:255'],
            'button_label' => ['nullable', 'string', 'max:120'],
            'button_url' => ['nullable', 'string', 'max:255'],
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
