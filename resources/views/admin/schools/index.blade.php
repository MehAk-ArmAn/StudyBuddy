@extends('layouts.admin')
@section('title', 'Schools & Security')
@section('content')
<div class="sb-school-admin" style="max-width:1100px;margin:0 auto;padding:20px;color:#f6f7ff">
<h1 style="font-size:clamp(2rem,4vw,3rem)">Schools & Security</h1><p>Never approve an institution on email-domain possession alone. Validate the school's legal identity and staff authorization independently before checking the verification box.</p>
@if(session('status'))<p role="status" style="padding:16px;background:#175d4e;border-radius:14px">{{ session('status') }}</p>@endif
@foreach($organizations as $organization)
<article style="background:#18203d;padding:24px;border:1px solid #485478;border-radius:22px;margin:18px 0">
<div style="display:flex;justify-content:space-between;flex-wrap:wrap;gap:14px"><div><h2 style="font-size:1.4rem;margin:0">{{ $organization->name }}</h2><p>{{ $organization->domain }} · {{ $organization->city }}, {{ $organization->country }}</p></div><strong style="text-transform:uppercase;letter-spacing:.12em">{{ $organization->status }}</strong></div>
<p><a href="{{ route('admin.control-room.schools.overview', $organization) }}" style="display:inline-flex;padding:9px 14px;border:1px solid #7f86c4;border-radius:10px;color:white;text-decoration:none">Open governance &amp; staff overview →</a></p>
<p><b>Requester:</b> {{ $organization->owner?->name }} ({{ $organization->owner?->email }})<br><b>School contact:</b> {{ $organization->official_email }}<br><b>Official site:</b> {{ $organization->website ?: 'Not supplied' }}</p>
<p style="white-space:pre-line">{{ $organization->purpose }}</p>
@if($organization->status === 'pending')<form method="POST" action="{{ route('admin.control-room.schools.review', $organization) }}">@csrf<label>Review outcome <select name="status" required style="width:100%;padding:12px;margin:6px 0 12px"><option value="rejected">Reject / request resubmission</option><option value="approved">Approve after independent checks</option></select></label><label>Internal review note <textarea name="review_note" rows="3" minlength="15" maxlength="2000" required style="width:100%;padding:12px;margin:6px 0 12px" placeholder="What documentary or official-channel evidence was independently checked?"></textarea></label><label style="display:block;margin:0 0 16px"><input type="checkbox" name="verified_documents" value="1"> I personally verified school identity and requester authorization before approval</label><button style="background:#8154f7;color:white;border:0;border-radius:12px;padding:12px 22px;cursor:pointer" type="submit">Save review</button></form>@else<p><small>Reviewed: {{ $organization->reviewed_at?->format('j M Y H:i') }} by {{ $organization->reviewer?->name }}</small></p><p>{{ $organization->review_note }}</p>@endif
</article>
@endforeach
{{ $organizations->links() }}
<h2 style="font-size:1.7rem;margin-top:38px">Recent security events</h2><p>These are audit signals, not proof that attacks are prevented. Rotate logs and review regularly.</p><div style="overflow:auto"><table style="width:100%;border-collapse:collapse"><thead><tr><th style="text-align:left;padding:10px">Time</th><th style="text-align:left;padding:10px">Event</th><th style="text-align:left;padding:10px">Severity</th><th style="text-align:left;padding:10px">IP</th></tr></thead><tbody>@forelse($events as $event)<tr style="border-top:1px solid #3a486a"><td style="padding:10px">{{ $event->created_at?->format('j M H:i') }}</td><td style="padding:10px">{{ $event->event }}</td><td style="padding:10px">{{ $event->severity }}</td><td style="padding:10px">{{ $event->ip_address }}</td></tr>@empty<tr><td colspan="4">No events have been recorded yet.</td></tr>@endforelse</tbody></table></div>
</div>
@endsection
