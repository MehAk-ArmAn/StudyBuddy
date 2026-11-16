<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\MediaAsset;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class MediaAssetController extends Controller
{
    public function index(): View { return view('admin.resources.index', array('title'=>'Media Assets', 'route'=>'admin.media-assets', 'fields'=>array('title', 'alt_text', 'path', 'type', 'mime_type', 'file_size', 'width', 'height', 'is_active'), 'items'=>MediaAsset::query()->orderBy('sort_order')->orderBy('id')->paginate(30))); }
    public function create(): View { return view('admin.resources.form', array('title'=>'Create Media Assets', 'route'=>'admin.media-assets', 'fields'=>array('title', 'alt_text', 'path', 'type', 'mime_type', 'file_size', 'width', 'height', 'is_active'), 'item'=>new MediaAsset(), 'method'=>'POST')); }
    public function store(Request $request): RedirectResponse { $data=$this->validated($request); MediaAsset::create($data); return redirect()->route('admin.media-assets.index')->with('status','Saved.'); }
    public function edit(MediaAsset $mediaAsset): View { return view('admin.resources.form', array('title'=>'Edit Media Assets', 'route'=>'admin.media-assets', 'fields'=>array('title', 'alt_text', 'path', 'type', 'mime_type', 'file_size', 'width', 'height', 'is_active'), 'item'=>$mediaAsset, 'method'=>'PUT')); }
    public function update(Request $request, MediaAsset $mediaAsset): RedirectResponse { $mediaAsset->update($this->validated($request)); return redirect()->route('admin.media-assets.index')->with('status','Saved.'); }
    public function destroy(MediaAsset $mediaAsset): RedirectResponse { $mediaAsset->delete(); return back()->with('status','Deleted.'); }
    private function validated(Request $request): array { $data=$request->validate(array('title'=>array('required','string','max:160'), 'alt_text'=>array('nullable','string','max:255'), 'path'=>array('required','string','max:255'), 'type'=>array('required','string','max:80'), 'mime_type'=>array('nullable','string','max:120'), 'file_size'=>array('nullable','integer','min:0'), 'width'=>array('nullable','integer','min:0'), 'height'=>array('nullable','integer','min:0'), 'is_active'=>array('sometimes','boolean'))); foreach (array('is_active') as $field) { $data[$field]=$request->boolean($field); } if (isset($data['settings']) && is_string($data['settings'])) { $data['settings']=json_decode($data['settings'], true); } return $data; }
}
