<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\HomepageSection;
use App\Models\HomepageSectionItem;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\View\View;

class HomepageSectionItemController extends Controller
{
    private array $fields = ['item_key','title','subtitle','body','image_path','icon_path','button_label','button_url','badge_text','sort_order','is_enabled','settings'];
    public function index(HomepageSection $homepageSection): View { return view('admin.resources.index', ['title'=>'Section Items: '.$homepageSection->title, 'route'=>'admin.homepage-sections.items', 'parent'=>$homepageSection, 'fields'=>$this->fields, 'items'=>$homepageSection->items()->paginate(30)]); }
    public function create(HomepageSection $homepageSection): View { return view('admin.resources.form', ['title'=>'Create Section Item', 'route'=>'admin.homepage-sections.items', 'parent'=>$homepageSection, 'fields'=>$this->fields, 'item'=>new HomepageSectionItem(), 'method'=>'POST']); }
    public function store(Request $request, HomepageSection $homepageSection): RedirectResponse { $homepageSection->items()->create($this->validated($request)); return redirect()->route('admin.homepage-sections.items.index', $homepageSection)->with('status','Saved.'); }
    public function edit(HomepageSection $homepageSection, HomepageSectionItem $item): View { return view('admin.resources.form', ['title'=>'Edit Section Item', 'route'=>'admin.homepage-sections.items', 'parent'=>$homepageSection, 'fields'=>$this->fields, 'item'=>$item, 'method'=>'PUT']); }
    public function update(Request $request, HomepageSection $homepageSection, HomepageSectionItem $item): RedirectResponse { $item->update($this->validated($request)); return redirect()->route('admin.homepage-sections.items.index', $homepageSection)->with('status','Saved.'); }
    public function destroy(HomepageSection $homepageSection, HomepageSectionItem $item): RedirectResponse { $item->delete(); return back()->with('status','Deleted.'); }
    private function validated(Request $request): array { $data=$request->validate(['item_key'=>['nullable','string','max:120'],'title'=>['nullable','string','max:255'],'subtitle'=>['nullable','string'],'body'=>['nullable','string'],'image_path'=>['nullable','string','max:255'],'icon_path'=>['nullable','string','max:255'],'button_label'=>['nullable','string','max:120'],'button_url'=>['nullable','string','max:255'],'badge_text'=>['nullable','string','max:120'],'sort_order'=>['required','integer','min:0'],'is_enabled'=>['sometimes','boolean'],'settings'=>['nullable','json']]); $data['is_enabled']=$request->boolean('is_enabled'); if(isset($data['settings'])) $data['settings']=json_decode($data['settings'], true); return $data; }
}
