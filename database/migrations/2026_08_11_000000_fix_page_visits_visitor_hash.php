<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Replace the broken per-session dedup with a real IP+User-Agent visitor hash.
     *
     * The original migration's `unique('session', 'ip_address')` created a unique
     * index on the `session` column alone, *named* "ip_address" (the second arg to
     * unique() is the index name, not a second column). That keyed dedup on the
     * session cookie, so cookie-less bots inserted a fresh row on every request.
     */
    public function up(): void
    {
        // Drop the misnamed single-column unique index. IF EXISTS keeps this safe
        // on fresh installs and if it has already been removed. (SQLite syntax.)
        DB::statement('DROP INDEX IF EXISTS ip_address');

        Schema::table('page_visits', function (Blueprint $table) {
            if (! Schema::hasColumn('page_visits', 'visitor_hash')) {
                $table->string('visitor_hash', 64)->nullable()->after('session');
            }
            if (! Schema::hasColumn('page_visits', 'user_agent')) {
                $table->string('user_agent')->nullable()->after('visitor_hash');
            }
        });

        // Real unique index on the new dedup key. Existing rows keep
        // visitor_hash = NULL; SQLite treats NULLs as distinct in a UNIQUE index,
        // so the index builds without a duplicate-key error and historical rows
        // are left untouched (the displayed count is recomputed from distinct IPs).
        Schema::table('page_visits', function (Blueprint $table) {
            $table->unique('visitor_hash');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('page_visits', function (Blueprint $table) {
            $table->dropUnique('page_visits_visitor_hash_unique');
        });

        Schema::table('page_visits', function (Blueprint $table) {
            $table->dropColumn(['visitor_hash', 'user_agent']);
        });
    }
};
