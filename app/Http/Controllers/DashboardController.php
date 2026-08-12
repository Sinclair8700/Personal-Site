<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\PageVisit;
use App\Models\Project;

class DashboardController extends Controller
{
    public function index()
    {
        // Both charts cover the same rolling 30-day window.
        $since = now()->subDays(29)->startOfDay();

        // New unique visitors per day. Each PageVisit row is one unique visitor
        // (deduped by IP+UA hash) and created_at is when they were first seen,
        // so grouping by date gives daily new visitors.
        $visitorsDaily = PageVisit::where('created_at', '>=', $since)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $visitorTrend = collect(range(0, 29))->map(function ($i) use ($visitorsDaily) {
            $date = now()->subDays(29 - $i)->startOfDay();

            return [
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('M j'),
                'count' => (int) $visitorsDaily->get($date->format('Y-m-d'), 0),
            ];
        });

        // Messages received in the window, newest first, grouped by day so each
        // bar can carry its own messages for the hover/click readout table.
        $messages = Contact::where('created_at', '>=', $since)
            ->latest()
            ->get(['email_address', 'message', 'created_at']);

        $messagesByDay = $messages->groupBy(fn ($message) => $message->created_at->format('Y-m-d'));

        $messageTrend = collect(range(0, 29))->map(function ($i) use ($messagesByDay) {
            $date = now()->subDays(29 - $i)->startOfDay();
            $dayMessages = $messagesByDay->get($date->format('Y-m-d'), collect());

            return [
                'date' => $date->format('Y-m-d'),
                'label' => $date->format('M j'),
                'count' => $dayMessages->count(),
                'items' => $dayMessages->map(fn ($message) => [
                    'time' => $message->created_at->format('g:i a'),
                    'email' => $message->email_address,
                    'message' => $message->message,
                ])->values(),
            ];
        });

        return view('dashboard.index', [
            'title' => 'Dashboard',
            'projectCount' => Project::count(),
            'messageCount' => Contact::count(),
            'uniqueVisitors' => PageVisit::uniqueVisitorCount(),
            'visitorTrend' => $visitorTrend,
            'messageTrend' => $messageTrend,
            'messages' => $messages,
        ]);
    }
}
