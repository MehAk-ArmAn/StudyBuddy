<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SiteSetting;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class SiteSettingController extends Controller
{
    public function index(): View { return view('admin.resources.index', array('title'=>'Site Settings', 'route'=>'admin.site-settings', 'fields'=>array('key', 'value', 'type', 'group'), 'items'=>SiteSetting::query()->orderBy('sort_order')->orderBy('id')->paginate(30))); }
    public function create(): View { return view('admin.resources.form', array('title'=>'Create Site Settings', 'route'=>'admin.site-settings', 'fields'=>array('key', 'value', 'type', 'group'), 'item'=>new SiteSetting(), 'method'=>'POST')); }
    public function store(Request $request): RedirectResponse { $data=$this->validated($request); SiteSetting::create($data); return redirect()->route('admin.site-settings.index')->with('status','Saved.'); }
    public function edit(SiteSetting $siteSetting): View { return view('admin.resources.form', array('title'=>'Edit Site Settings', 'route'=>'admin.site-settings', 'fields'=>array('key', 'value', 'type', 'group'), 'item'=>$siteSetting, 'method'=>'PUT')); }
    public function update(Request $request, SiteSetting $siteSetting): RedirectResponse { $siteSetting->update($this->validated($request)); return redirect()->route('admin.site-settings.index')->with('status','Saved.'); }
    public function destroy(SiteSetting $siteSetting): RedirectResponse { $siteSetting->delete(); return back()->with('status','Deleted.'); }
    private function validated(Request $request): array { $data=$request->validate(array('key'=>array('required','string','max:120'), 'value'=>array('nullable','string'), 'type'=>array('required','string','max:40'), 'group'=>array('required','string','max:80'))); foreach (array() as $field) { $data[$field]=$request->boolean($field); } if (isset($data['settings']) && is_string($data['settings'])) { $data['settings']=json_decode($data['settings'], true); } return $data; }
}
