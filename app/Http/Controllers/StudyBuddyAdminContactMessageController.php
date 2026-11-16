<?php

namespace App\Http\Controllers;

use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class StudyBuddyAdminContactMessageController extends Controller
{
    public function index(Request $request): View
    {
        abort_unless(Schema::hasTable('studybuddy_contact_messages'), 404);

        $query = DB::table('studybuddy_contact_messages')->latest('id');

        if ($request->query('status')) {
            $query->where('status', $request->query('status'));
        }

        if ($request->query('category')) {
            $query->where('category', $request->query('category'));
        }

        $base = DB::table('studybuddy_contact_messages');

        return view('admin.contact-messages.index', [
            'messages' => $query->paginate(20)->withQueryString(),
            'stats' => [
                'total' => (clone $base)->count(),
                'new' => (clone $base)->where('status', 'new')->count(),
                'priority' => (clone $base)->whereIn('priority', ['high', 'urgent'])->count(),
                'resolved' => (clone $base)->where('status', 'resolved')->count(),
            ],
        ]);
    }

    public function show(int $message): View
    {
        $item = DB::table('studybuddy_contact_messages')->where('id', $message)->first();
        abort_unless($item, 404);

        if (!$item->read_at) {
            DB::table('studybuddy_contact_messages')->where('id', $message)->update([
                'read_at' => now(),
                'status' => $item->status === 'new' ? 'read' : $item->status,
                'updated_at' => now(),
            ]);

            $item = DB::table('studybuddy_contact_messages')->where('id', $message)->first();
        }

        return view('admin.contact-messages.show', ['message' => $item]);
    }

    public function update(Request $request, int $message): RedirectResponse
    {
        $data = $request->validate([
            'status' => ['required', 'in:new,read,in-progress,resolved,archived'],
            'priority' => ['required', 'in:low,normal,high,urgent'],
        ]);

        DB::table('studybuddy_contact_messages')->where('id', $message)->update([
            'status' => $data['status'],
            'priority' => $data['priority'],
            'updated_at' => now(),
        ]);

        return back()->with('status', 'Message updated.');
    }
}
