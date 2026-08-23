<?php

namespace App\Http\Controllers;

use App\Models\HomepageSection;
use App\Models\HomepageSectionItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudyBuddyAdminHomepageCmsController extends Controller
{
    public function index(): View
    {
        $sections = HomepageSection::query()
            ->with(['items' => fn ($query) => $query->orderBy('sort_order')->orderBy('id')])
            ->orderBy('sort_order')
            ->orderBy('id')
            ->get();

        $items = $sections->mapWithKeys(
            fn (HomepageSection $section): array => [$section->id => $section->items]
        );

        $databaseVisuals = $sections
            ->pluck('image_path')
            ->merge($sections->flatMap(fn (HomepageSection $section) => $section->items->pluck('image_path')))
            ->filter();

        $visuals = $databaseVisuals
            ->merge([
                '/assets/studybuddy-brand/pages/hero-dolphin-book.webp',
                '/assets/studybuddy-brand/pages/path-apps.webp',
                '/assets/studybuddy-brand/pages/path-apps.webp',
                '/assets/studybuddy-brand/pages/path-parents.webp',
                '/assets/studybuddy-brand/pages/path-teachers.webp',
            ])
            ->unique()
            ->values()
            ->take(18);

        return view('admin.homepage-cms.index', compact('sections', 'items', 'visuals'));
    }

    public function updateSection(Request $request, HomepageSection $section): RedirectResponse
    {
        $data = $request->validate([
            'eyebrow' => ['nullable', 'string', 'max:190'],
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:2000'],
            'body' => ['nullable', 'string', 'max:12000'],
            'image_path' => ['nullable', 'string', 'max:1000'],
            'button_label' => ['nullable', 'string', 'max:190'],
            'button_url' => ['nullable', 'string', 'max:1000'],
            'secondary_button_label' => ['nullable', 'string', 'max:190'],
            'secondary_button_url' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'is_enabled' => ['nullable', 'boolean'],
        ]);

        $data['is_enabled'] = $request->boolean('is_enabled');
        $section->update($data);

        return back()->with('status', "{$section->title} homepage section updated.");
    }

    public function updateItem(Request $request, HomepageSectionItem $item): RedirectResponse
    {
        $data = $request->validate([
            'badge_text' => ['nullable', 'string', 'max:120'],
            'title' => ['required', 'string', 'max:255'],
            'subtitle' => ['nullable', 'string', 'max:1200'],
            'body' => ['nullable', 'string', 'max:6000'],
            'image_path' => ['nullable', 'string', 'max:1000'],
            'button_label' => ['nullable', 'string', 'max:190'],
            'button_url' => ['nullable', 'string', 'max:1000'],
            'sort_order' => ['nullable', 'integer', 'min:0', 'max:10000'],
            'is_enabled' => ['nullable', 'boolean'],
        ]);

        $data['is_enabled'] = $request->boolean('is_enabled');
        $item->update($data);

        return back()->with('status', "{$item->title} homepage card updated.");
    }
}
