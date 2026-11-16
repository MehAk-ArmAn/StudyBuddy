<?php

namespace App\Http\Controllers;

use App\Models\StudyBuddyMailingSubscriber;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\StreamedResponse;

class StudyBuddyMailingListController extends Controller
{
    public function subscribe(Request $request): RedirectResponse
    {
        if (filled($request->input('website'))) {
            return back()->with(
                'newsletter_success',
                'You are now on the StudyBuddy updates list.'
            );
        }

        $request->merge([
            'email' => Str::lower(
                trim((string) $request->input('email'))
            ),
        ]);

        $validated = $request->validate(
            [
                'email' => [
                    'required',
                    'string',
                    'email:rfc',
                    'max:254',
                ],
            ],
            [
                'email.required' => 'Enter your email address.',
                'email.email' => 'Enter a valid email address.',
            ]
        );

        $subscriber = StudyBuddyMailingSubscriber::query()
            ->firstOrNew([
                'email' => $validated['email'],
            ]);

        $alreadyActive =
            $subscriber->exists
            && $subscriber->status === 'active';

        $subscriber->fill([
            'status' => 'active',
            'source' => 'website_updates',
            'subscribed_at' => $alreadyActive
                ? $subscriber->subscribed_at
                : now(),
            'unsubscribed_at' => null,
            'ip_hash' => hash(
                'sha256',
                (string) $request->ip()
                .'|'
                .config('app.key')
            ),
            'user_agent' => Str::limit(
                (string) $request->userAgent(),
                500,
                ''
            ),
        ]);

        $subscriber->save();

        return back()->with(
            'newsletter_success',
            $alreadyActive
                ? 'You are already on the StudyBuddy updates list.'
                : 'You have joined the StudyBuddy updates list.'
        );
    }

    public function adminIndex(Request $request): View
    {
        $this->authorizeAdmin($request);

        $search = trim(
            (string) $request->query('q', '')
        );

        $status = trim(
            (string) $request->query('status', '')
        );

        $query = StudyBuddyMailingSubscriber::query()
            ->latest('subscribed_at')
            ->latest('id');

        if ($search !== '') {
            $query->where(
                'email',
                'like',
                '%'.$search.'%'
            );
        }

        if (in_array($status, ['active', 'unsubscribed'], true)) {
            $query->where('status', $status);
        }

        $subscribers = $query
            ->paginate(25)
            ->withQueryString();

        $stats = [
            'total' => StudyBuddyMailingSubscriber::query()->count(),

            'active' => StudyBuddyMailingSubscriber::query()
                ->where('status', 'active')
                ->count(),

            'unsubscribed' => StudyBuddyMailingSubscriber::query()
                ->where('status', 'unsubscribed')
                ->count(),

            'today' => StudyBuddyMailingSubscriber::query()
                ->whereDate('subscribed_at', today())
                ->count(),
        ];

        return view(
            'admin.mailing-list.index',
            compact(
                'subscribers',
                'stats',
                'search',
                'status'
            )
        );
    }

    public function adminUpdate(
        Request $request,
        StudyBuddyMailingSubscriber $subscriber
    ): RedirectResponse {
        $this->authorizeAdmin($request);

        $validated = $request->validate([
            'status' => [
                'required',
                'in:active,unsubscribed',
            ],
        ]);

        $subscriber->update([
            'status' => $validated['status'],

            'subscribed_at' =>
                $validated['status'] === 'active'
                    ? ($subscriber->subscribed_at ?: now())
                    : $subscriber->subscribed_at,

            'unsubscribed_at' =>
                $validated['status'] === 'unsubscribed'
                    ? now()
                    : null,
        ]);

        return back()->with(
            'mailing_list_status',
            'Subscriber status updated.'
        );
    }

    public function adminDestroy(
        Request $request,
        StudyBuddyMailingSubscriber $subscriber
    ): RedirectResponse {
        $this->authorizeAdmin($request);

        $subscriber->delete();

        return back()->with(
            'mailing_list_status',
            'Subscriber removed from the mailing list.'
        );
    }

    public function adminExport(Request $request): StreamedResponse
    {
        $this->authorizeAdmin($request);

        $filename =
            'studybuddy-mailing-list-'
            .now()->format('Y-m-d-His')
            .'.csv';

        return response()->streamDownload(
            function (): void {
                $output = fopen('php://output', 'w');

                fputcsv($output, [
                    'Email',
                    'Status',
                    'Source',
                    'Subscribed At',
                    'Unsubscribed At',
                    'Created At',
                ]);

                StudyBuddyMailingSubscriber::query()
                    ->orderBy('id')
                    ->chunkById(
                        500,
                        function ($subscribers) use ($output): void {
                            foreach ($subscribers as $subscriber) {
                                fputcsv($output, [
                                    $subscriber->email,
                                    $subscriber->status,
                                    $subscriber->source,
                                    optional(
                                        $subscriber->subscribed_at
                                    )->toDateTimeString(),
                                    optional(
                                        $subscriber->unsubscribed_at
                                    )->toDateTimeString(),
                                    optional(
                                        $subscriber->created_at
                                    )->toDateTimeString(),
                                ]);
                            }
                        }
                    );

                fclose($output);
            },
            $filename,
            [
                'Content-Type' => 'text/csv',
            ]
        );
    }

    private function authorizeAdmin(Request $request): void
    {
        abort_unless(
            $request->user()
            && (bool) $request->user()->is_admin,
            403
        );
    }
}
