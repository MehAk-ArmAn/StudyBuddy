<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Page;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class PageController extends Controller
{
    private array $fields = [
        'slug', 'template', 'title', 'nav_label', 'meta_title', 'meta_description',
        'eyebrow', 'hero_title', 'hero_subtitle', 'hero_body', 'hero_image_path',
        'button_label', 'button_url', 'secondary_button_label', 'secondary_button_url',
        'sort_order', 'is_enabled', 'settings',
    ];

    public function index(): View
    {
        return view('admin.resources.index', [
            'title' => 'Pages',
            'route' => 'admin.pages',
            'fields' => $this->fields,
            'items' => Page::query()->orderBy('sort_order')->orderBy('id')->paginate(30),
        ]);
    }

    public function create(): View
    {
        return view('admin.resources.form', [
            'title' => 'Create Page',
            'route' => 'admin.pages',
            'fields' => $this->fields,
            'item' => new Page(),
            'method' => 'POST',
        ]);
    }

    public function store(Request $request): RedirectResponse
    {
        Page::create($this->validated($request));
        return redirect()->route('admin.pages.index')->with('status', 'Saved.');
    }

    public function edit(Page $page): View
    {
        return view('admin.resources.form', [
            'title' => 'Edit Page',
            'route' => 'admin.pages',
            'fields' => $this->fields,
            'item' => $page,
            'method' => 'PUT',
        ]);
    }

    public function update(Request $request, Page $page): RedirectResponse
    {
        $page->update($this->validated($request));
        return redirect()->route('admin.pages.index')->with('status', 'Saved.');
    }

    public function destroy(Page $page): RedirectResponse
    {
        $page->delete();
        return back()->with('status', 'Deleted.');
    }

    private function validated(Request $request): array
    {
        $data = $request->validate([
            'slug' => ['required', 'string', 'max:160'],
            'template' => ['required', 'string', 'max:80'],
            'title' => ['required', 'string', 'max:255'],
            'nav_label' => ['nullable', 'string', 'max:120'],
            'meta_title' => ['nullable', 'string', 'max:255'],
            'meta_description' => ['nullable', 'string'],
            'eyebrow' => ['nullable', 'string', 'max:160'],
            'hero_title' => ['nullable', 'string', 'max:255'],
            'hero_subtitle' => ['nullable', 'string'],
            'hero_body' => ['nullable', 'string'],
            'hero_image_path' => ['nullable', 'string', 'max:255'],
            'button_label' => ['nullable', 'string', 'max:120'],
            'button_url' => ['nullable', 'string', 'max:255'],
            'secondary_button_label' => ['nullable', 'string', 'max:120'],
            'secondary_button_url' => ['nullable', 'string', 'max:255'],
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
