<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\NavigationItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class NavigationItemController extends Controller
{
    public function index(): View { return view('admin.resources.index', array('title'=>'Navigation Items', 'route'=>'admin.navigation-items', 'fields'=>array('label', 'url', 'sort_order', 'is_enabled', 'opens_new_tab'), 'items'=>NavigationItem::query()->orderBy('sort_order')->orderBy('id')->paginate(30))); }
    public function create(): View { return view('admin.resources.form', array('title'=>'Create Navigation Items', 'route'=>'admin.navigation-items', 'fields'=>array('label', 'url', 'sort_order', 'is_enabled', 'opens_new_tab'), 'item'=>new NavigationItem(), 'method'=>'POST')); }
    public function store(Request $request): RedirectResponse { $data=$this->validated($request); NavigationItem::create($data); return redirect()->route('admin.navigation-items.index')->with('status','Saved.'); }
    public function edit(NavigationItem $navigationItem): View { return view('admin.resources.form', array('title'=>'Edit Navigation Items', 'route'=>'admin.navigation-items', 'fields'=>array('label', 'url', 'sort_order', 'is_enabled', 'opens_new_tab'), 'item'=>$navigationItem, 'method'=>'PUT')); }
    public function update(Request $request, NavigationItem $navigationItem): RedirectResponse { $navigationItem->update($this->validated($request)); return redirect()->route('admin.navigation-items.index')->with('status','Saved.'); }
    public function destroy(NavigationItem $navigationItem): RedirectResponse { $navigationItem->delete(); return back()->with('status','Deleted.'); }
    private function validated(Request $request): array { $data=$request->validate(array('label'=>array('required','string','max:120'), 'url'=>array('required','string','max:255'), 'sort_order'=>array('required','integer','min:0'), 'is_enabled'=>array('sometimes','boolean'), 'opens_new_tab'=>array('sometimes','boolean'))); foreach (array('is_enabled', 'opens_new_tab') as $field) { $data[$field]=$request->boolean($field); } if (isset($data['settings']) && is_string($data['settings'])) { $data['settings']=json_decode($data['settings'], true); } return $data; }
}
