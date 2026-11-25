<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    private const SLUG = 'pool-snooker-simulator';

    private const OLD = 'A cross platform pool & snooker simulator game with togglable rules and custom game modes. I built this project as a part of my games computing course at university. It uses openframeworks for the graphics and open dynamics engine for physics.';

    private const NEW = 'A 3D pool and snooker game with rules you can switch on and off and two custom game modes, built for my Games Computing module at Keele University. Balls, cushions and pockets run on the Open Dynamics Engine, with openFrameworks for the graphics and a cue I modelled in Blender. Golden Pocket and Evil Pocket modes hand out random rewards and punishments for potting into a highlighted pocket. Written in C++.';

    // The write-up is resources/views/projects/projects/pool-snooker-simulator/index.blade.php.
    public function up(): void
    {
        DB::table('projects')->where('slug', self::SLUG)->update(['description' => self::NEW, 'updated_at' => now()]);
        Cache::forget('nav.projects');
    }

    public function down(): void
    {
        DB::table('projects')->where('slug', self::SLUG)->update(['description' => self::OLD]);
        Cache::forget('nav.projects');
    }
};
