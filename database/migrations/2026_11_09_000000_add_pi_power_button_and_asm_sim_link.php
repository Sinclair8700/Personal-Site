<?php

use App\Models\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    private const PI_SLUG = 'pi-power-button';

    /** Placeholder links from the initial seed; project pages now show the link, so these go. */
    private const PLACEHOLDER_LINKS = ['https://github.com/project1', 'http://google.com'];

    public function up(): void
    {
        // Pi Power Button: the write-up is resources/views/projects/projects/pi-power-button/index.blade.php
        // and its card image is main.png next to it, copied into public storage here.
        $project = Project::firstOrCreate(['slug' => self::PI_SLUG], [
            'name' => 'Pi Power Button',
            'description' => 'A Raspberry Pi Zero W wired to my PC\'s power switch through a relay, with a password-protected web page that turns the PC on and off from anywhere. It watches the network to confirm each press worked, and runs schedules, auto-off after two hours idle and a helper inside Windows for clean shutdowns. Built in Python with only the standard library.',
            'link' => null,
        ]);

        $disk = Storage::disk('public');
        $source = resource_path('views/projects/projects/'.self::PI_SLUG.'/main.png');
        $target = 'projects/'.self::PI_SLUG.'/0.png';
        if (! $disk->exists($target) && is_file($source)) {
            $disk->put($target, file_get_contents($source));
        }
        if (! DB::table('project_images')->where('project_id', $project->id)->exists()) {
            DB::table('project_images')->insert([
                'project_id' => $project->id,
                'filename' => '0.png',
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        DB::table('projects')->whereIn('link', self::PLACEHOLDER_LINKS)->update(['link' => null]);

        // ASM Sim & Learn (third year project) is served as a static site from the web root.
        DB::table('projects')->where('slug', 'assembly-simulator')->update(['link' => 'https://alexdavi.es/asm-sim/']);

        Cache::forget('nav.projects');
    }

    public function down(): void
    {
        // Not Project::delete(): that also deletes the write-up view from resources/.
        $id = DB::table('projects')->where('slug', self::PI_SLUG)->value('id');
        if ($id) {
            DB::table('project_images')->where('project_id', $id)->delete();
            DB::table('projects')->where('id', $id)->delete();
            Storage::disk('public')->deleteDirectory('projects/'.self::PI_SLUG);
        }

        DB::table('projects')->where('slug', 'assembly-simulator')->update(['link' => null]);

        Cache::forget('nav.projects');
    }
};
