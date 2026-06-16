<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\FooterItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class FooterItemController extends Controller
{
    public function index(): View { return view('admin.resources.index', array('title'=>'Footer Items', 'route'=>'admin.footer-items', 'fields'=>array('group', 'label', 'url', 'sort_order', 'is_enabled', 'opens_new_tab'), 'items'=>FooterItem::query()->orderBy('sort_order')->orderBy('id')->paginate(30))); }
    public function create(): View { return view('admin.resources.form', array('title'=>'Create Footer Items', 'route'=>'admin.footer-items', 'fields'=>array('group', 'label', 'url', 'sort_order', 'is_enabled', 'opens_new_tab'), 'item'=>new FooterItem(), 'method'=>'POST')); }
    public function store(Request $request): RedirectResponse { $data=$this->validated($request); FooterItem::create($data); return redirect()->route('admin.footer-items.index')->with('status','Saved.'); }
    public function edit(FooterItem $footerItem): View { return view('admin.resources.form', array('title'=>'Edit Footer Items', 'route'=>'admin.footer-items', 'fields'=>array('group', 'label', 'url', 'sort_order', 'is_enabled', 'opens_new_tab'), 'item'=>$footerItem, 'method'=>'PUT')); }
    public function update(Request $request, FooterItem $footerItem): RedirectResponse { $footerItem->update($this->validated($request)); return redirect()->route('admin.footer-items.index')->with('status','Saved.'); }
    public function destroy(FooterItem $footerItem): RedirectResponse { $footerItem->delete(); return back()->with('status','Deleted.'); }
    private function validated(Request $request): array { $data=$request->validate(array('group'=>array('required','string','max:80'), 'label'=>array('required','string','max:120'), 'url'=>array('required','string','max:255'), 'sort_order'=>array('required','integer','min:0'), 'is_enabled'=>array('sometimes','boolean'), 'opens_new_tab'=>array('sometimes','boolean'))); foreach (array('is_enabled', 'opens_new_tab') as $field) { $data[$field]=$request->boolean($field); } if (isset($data['settings']) && is_string($data['settings'])) { $data['settings']=json_decode($data['settings'], true); } return $data; }
}
