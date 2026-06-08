@extends('admin.layouts.admin')
@section('title', 'Footer')
@section('heading', 'Footer Manager')
@section('content')
<form method="POST" action="{{ route('admin.footer.save') }}" class="admin-cms-panel">@csrf @method('PUT')<h2>Footer Text</h2><label>Tagline<input name="settings[footer_tagline]" value="{{ \App\Support\Cms::setting('footer_tagline', 'Learn. Play. Grow. Your Way.') }}"></label><label>Copyright<input name="settings[footer_copyright]" value="{{ \App\Support\Cms::setting('footer_copyright', 'StudyBuddy. A safe cosmic learning universe for every learner.') }}"></label>@foreach($sections as $section)<section class="admin-cms-edit-section"><h2>{{ $section->title }}</h2>@foreach($section->links as $link)<div class="admin-cms-row"><input name="links[{{ $link->id }}][label]" value="{{ $link->label }}"><input name="links[{{ $link->id }}][url]" value="{{ $link->url }}"><input name="links[{{ $link->id }}][route_name]" value="{{ $link->route_name }}"><input name="links[{{ $link->id }}][sort_order]" type="number" value="{{ $link->sort_order }}"></div>@endforeach</section>@endforeach<button class="admin-cms-save">Save Footer</button></form>
@endsection
