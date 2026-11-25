<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Most important first. This drives the Projects dropdown and the /projects page.
     * Projects not listed here (e.g. added later through the admin form) keep the
     * default of 1000 and appear after these, oldest first.
     */
    private const ORDER = [
        'the-print-shop-tunstall',
        'bird-box-shop',
        'lil-chongee-bud-bro',
        'fibre-maps',
        'assembly-simulator',
        'pi-power-button',
        'pool-snooker-simulator',
        'devspeedup',
        'keele-book-store',
        'gpt-discord-bot',
        'arduino-pi-serial-rest-api',
        'basic-c-game-engine',
        'chess-game',
        'music-2',
        'co-production-game',
        'scenic-routes',
        'sim-shifter',
        'peugeot-208-gti',
        'old-site',
    ];

    public function up(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->unsignedInteger('sort_order')->default(1000)->after('link');
        });

        foreach (self::ORDER as $i => $slug) {
            DB::table('projects')->where('slug', $slug)->update(['sort_order' => ($i + 1) * 10]);
        }

        // Each save through the old edit form escaped "&" once more, so some descriptions
        // read "&amp;amp;amp;...". Collapse those back to a plain "&".
        foreach (DB::table('projects')->get(['id', 'name', 'description']) as $project) {
            $clean = [];
            foreach (['name', 'description'] as $field) {
                $value = $project->{$field};
                while (is_string($value) && str_contains($value, '&amp;')) {
                    $value = str_replace('&amp;', '&', $value);
                }
                if ($value !== $project->{$field}) {
                    $clean[$field] = $value;
                }
            }
            if ($clean) {
                DB::table('projects')->where('id', $project->id)->update($clean);
            }
        }

        Cache::forget('nav.projects');
    }

    public function down(): void
    {
        Schema::table('projects', function (Blueprint $table) {
            $table->dropColumn('sort_order');
        });

        Cache::forget('nav.projects');
    }
};
