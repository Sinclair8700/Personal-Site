<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Cache;

class PageVisit extends Model
{
    protected $fillable = ['ip_address', 'user_agent', 'session', 'visitor_hash', 'created_at'];

    /**
     * Unique visitor count, recomputed from distinct IP addresses and cached
     * briefly so it isn't a COUNT query on every page render.
     *
     * Counting distinct IPs (rather than rows) collapses the historical
     * per-session bot rows — many sessions from one IP — down to a realistic
     * unique-visitor figure, and never uses the old max('id') that only climbed.
     */
    public static function uniqueVisitorCount(): int
    {
        return Cache::remember(
            'page_visits:unique_count',
            now()->addMinutes(5),
            fn () => static::query()->distinct()->count('ip_address')
        );
    }
}
