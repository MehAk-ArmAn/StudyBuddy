<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomepageSectionController extends Controller
{
    public function index(): View { return view('admin.resources.index', array('title'=>'Homepage Sections', 'route'=>'admin.homepage-sections', 'fields'=>array('section_key', 'section_type', 'eyebrow', 'title', 'subtitle', 'body', 'image_path', 'background_image_path', 'button_label', 'button_url', 'secondary_button_label', 'secondary_button_url', 'sort_order', 'is_enabled', 'settings'), 'items'=>HomepageSection::query()->orderBy('sort_order')->orderBy('id')->paginate(30))); }
    public function create(): View { return view('admin.resources.form', array('title'=>'Create Homepage Sections', 'route'=>'admin.homepage-sections', 'fields'=>array('section_key', 'section_type', 'eyebrow', 'title', 'subtitle', 'body', 'image_path', 'background_image_path', 'button_label', 'button_url', 'secondary_button_label', 'secondary_button_url', 'sort_order', 'is_enabled', 'settings'), 'item'=>new HomepageSection(), 'method'=>'POST')); }
    public function store(Request $request): RedirectResponse { $data=$this->validated($request); HomepageSection::create($data); return redirect()->route('admin.homepage-sections.index')->with('status','Saved.'); }
    public function edit(HomepageSection $homepageSection): View { return view('admin.resources.form', array('title'=>'Edit Homepage Sections', 'route'=>'admin.homepage-sections', 'fields'=>array('section_key', 'section_type', 'eyebrow', 'title', 'subtitle', 'body', 'image_path', 'background_image_path', 'button_label', 'button_url', 'secondary_button_label', 'secondary_button_url', 'sort_order', 'is_enabled', 'settings'), 'item'=>$homepageSection, 'method'=>'PUT')); }
    public function update(Request $request, HomepageSection $homepageSection): RedirectResponse { $homepageSection->update($this->validated($request)); return redirect()->route('admin.homepage-sections.index')->with('status','Saved.'); }
    public function destroy(HomepageSection $homepageSection): RedirectResponse { $homepageSection->delete(); return back()->with('status','Deleted.'); }
    private function validated(Request $request): array { $data=$request->validate(array('section_key'=>array('required','string','max:120'), 'section_type'=>array('required','string','max:80'), 'eyebrow'=>array('nullable','string','max:160'), 'title'=>array('nullable','string','max:255'), 'subtitle'=>array('nullable','string'), 'body'=>array('nullable','string'), 'image_path'=>array('nullable','string','max:255'), 'background_image_path'=>array('nullable','string','max:255'), 'button_label'=>array('nullable','string','max:120'), 'button_url'=>array('nullable','string','max:255'), 'secondary_button_label'=>array('nullable','string','max:120'), 'secondary_button_url'=>array('nullable','string','max:255'), 'sort_order'=>array('required','integer','min:0'), 'is_enabled'=>array('sometimes','boolean'), 'settings'=>array('nullable','json'))); foreach (array('is_enabled') as $field) { $data[$field]=$request->boolean($field); } if (isset($data['settings']) && is_string($data['settings'])) { $data['settings']=json_decode($data['settings'], true); } return $data; }
}
