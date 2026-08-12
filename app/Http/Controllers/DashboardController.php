<?php

namespace App\Http\Controllers;

use App\Models\Contact;
use App\Models\PageVisit;
use App\Models\Project;

class DashboardController extends Controller
{
    public function index()
    {
        // New unique visitors per day over the last 30 days. Each PageVisit row
        // is one unique visitor (deduped by IP+UA hash) and created_at is when
        // they were first seen, so grouping by date gives daily new visitors.
        $since = now()->subDays(29)->startOfDay();

        $daily = PageVisit::where('created_at', '>=', $since)
            ->selectRaw('DATE(created_at) as day, COUNT(*) as total')
            ->groupBy('day')
            ->pluck('total', 'day');

        $trend = collect(range(0, 29))->map(function ($i) use ($daily) {
            $date = now()->subDays(29 - $i)->startOfDay();

            return [
                'label' => $date->format('M j'),
                'count' => (int) $daily->get($date->format('Y-m-d'), 0),
            ];
        });

        return view('dashboard.index', [
            'title' => 'Dashboard',
            'projectCount' => Project::count(),
            'messageCount' => Contact::count(),
            'uniqueVisitors' => PageVisit::uniqueVisitorCount(),
            'trend' => $trend,
        ]);
    }
}
