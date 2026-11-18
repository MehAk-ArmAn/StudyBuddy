<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolOrganization;
use App\Models\SchoolOrganizationMember;
use App\Models\StudyBuddySecurityEvent;
use App\Support\StudyBuddyAdminAuthority as Authority;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use Illuminate\View\View;

class SchoolOrganizationReviewController extends Controller
{
    public function index(): View
    {
        return view('admin.schools.index', [
            'organizations' => SchoolOrganization::with(['owner', 'reviewer'])->latest()->paginate(20),
            'events' => Authority::can(auth()->user(), 'ops.view')
                ? StudyBuddySecurityEvent::latest('created_at')->limit(30)->get()
                : collect(),
        ]);
    }

    public function update(Request $request, SchoolOrganization $organization): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', Rule::in(['approved', 'rejected'])],
            'review_note' => ['required', 'string', 'min:15', 'max:2000'],
            'verified_documents' => ['exclude_unless:status,approved', 'required', 'accepted'],
        ]);
        DB::transaction(function () use ($data, $request, $organization) {
            $org = SchoolOrganization::whereKey($organization->id)->lockForUpdate()->firstOrFail();
            abort_unless($org->status === 'pending', 409, 'Only pending applications can be reviewed.');
            $org->update([
                'status' => $data['status'],
                'review_note' => $data['review_note'],
                'reviewed_by' => $request->user()->id,
                'reviewed_at' => now(),
            ]);
            if ($data['status'] === 'approved') {
                SchoolOrganizationMember::updateOrCreate(
                    ['organization_id' => $org->id, 'user_id' => $org->requested_by],
                    ['role' => 'owner', 'status' => 'active', 'joined_at' => now()]
                );
            }
        });
        StudyBuddySecurityEvent::record('school_application_'.$data['status'], 'warning', $request->user()->id, ['organization_id' => $organization->id]);
        return back()->with('status', 'School application '.$data['status'].'. Keep the evidence of your offline verification separately in an access-controlled system.');
    }
}
