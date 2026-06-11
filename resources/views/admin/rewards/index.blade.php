@extends('admin.layouts.admin')
@section('title', 'Rewards Manager')
@section('heading', 'Rewards Manager')
@section('content')
@include('admin.partials.image-path-help')
<form method="POST" action="{{ route('admin.rewards.save') }}" class="admin-cms-panel">@csrf @method('PUT')<div class="admin-cms-table"><table><thead><tr><th>Name</th><th>Category</th><th>Coins</th><th>Image Path</th><th>Rarity</th><th>Active</th></tr></thead><tbody>@foreach($rewards as $reward)<tr><td><input name="rewards[{{ $reward->id }}][name]" value="{{ $reward->name }}"></td><td><input name="rewards[{{ $reward->id }}][category]" value="{{ $reward->category }}"></td><td><input type="number" name="rewards[{{ $reward->id }}][points_required]" value="{{ $reward->points_required }}"></td><td><input name="rewards[{{ $reward->id }}][image_path]" value="{{ $reward->image_path }}"></td><td><input name="rewards[{{ $reward->id }}][rarity]" value="{{ $reward->rarity }}"></td><td><input type="checkbox" name="rewards[{{ $reward->id }}][is_active]" @checked($reward->is_active)></td></tr>@endforeach</tbody></table></div><button class="admin-cms-save">Save Rewards</button></form>
@endsection
