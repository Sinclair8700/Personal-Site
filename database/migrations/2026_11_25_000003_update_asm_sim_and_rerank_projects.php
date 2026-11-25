<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Ranked by how technical and how relevant to clients and employers each project is.
     * monitor-launcher is listed ahead of being added, so it slots in when it arrives.
     */
    private const ORDER = [
        'the-print-shop-tunstall',
        'pi-power-button',
        'fibre-maps',
        'bird-box-shop',
        'assembly-simulator',
        'lil-chongee-bud-bro',
        'devspeedup',
        'monitor-launcher',
        'pool-snooker-simulator',
        'keele-book-store',
        'basic-c-game-engine',
        'arduino-pi-serial-rest-api',
        'gpt-discord-bot',
        'chess-game',
        'co-production-game',
        'music-2',
        'scenic-routes',
        'sim-shifter',
        'old-site',
        'peugeot-208-gti',
    ];

    private const ASM_OLD = 'A browser based 16 bit computer simulator which allows you to learn, write, assemble, run and share assembly code. I built this project as part of my university thesis on teaching people how to code in assembly language.';

    private const ASM_NEW = 'A 16-bit computer that runs in the browser, with lessons that check your program by scanning its memory and a community area where people share, rate and run each other\'s assembly. It was my final-year project at Keele University, and it is back online with ten example programs, from a bouncing ball on its 20x20 display to a rocket countdown across all three outputs.';

    public function up(): void
    {
        foreach (self::ORDER as $i => $slug) {
            DB::table('projects')->where('slug', $slug)->update(['sort_order' => ($i + 1) * 10]);
        }

        DB::table('projects')->where('slug', 'assembly-simulator')->update(['description' => self::ASM_NEW, 'updated_at' => now()]);

        Cache::forget('nav.projects');
    }

    public function down(): void
    {
        DB::table('projects')->where('slug', 'assembly-simulator')->update(['description' => self::ASM_OLD]);

        Cache::forget('nav.projects');
    }
};
