<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\SchoolOrganization;
use App\Models\StudyBuddySecurityEvent;
use App\Support\StudyBuddyAdminAuthority as Authority;
use Illuminate\Http\Request;
use Illuminate\View\View;

class StudyBuddyP11BSchoolOversightController extends Controller
{
    public function show(Request $request, SchoolOrganization $organization): View
    {
        abort_unless(Authority::can($request->user(), 'schools.review'), 403);
        $organization->load(['owner:id,name,email', 'reviewer:id,name',
            'members' => fn ($query) => $query->with('user:id,name,email')->orderBy('role'),
            'invitations' => fn ($query) => $query->select('id', 'organization_id', 'email', 'role', 'expires_at', 'accepted_at', 'revoked_at')->latest()->limit(30),
        ]);
        $audit = StudyBuddySecurityEvent::query()->whereIn('event', [
            'school_application_approved', 'school_application_rejected',
            'school_staff_access_revoked', 'school_staff_role_changed',
            'school_invitation_sent', 'school_invitation_revoked', 'school_invitation_accepted',
        ])->where('context->organization_id', $organization->id)
            ->latest('created_at')->limit(30)->get();
        return view('admin.p11b.organization', compact('organization', 'audit'));
    }
}
