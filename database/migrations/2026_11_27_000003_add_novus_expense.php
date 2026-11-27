<?php

use App\Models\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    private const SLUG = 'novus-expense';

    // The write-up is resources/views/projects/projects/novus-expense/index.blade.php and its card
    // image is main.png next to it (the travel booking form), copied into public storage here.
    public function up(): void
    {
        $project = Project::firstOrCreate(['slug' => self::SLUG], [
            'name' => 'Novus Expense',
            'description' => 'The expenses and travel booking system I built for Novus Property Solutions in 2019. Staff claim back expenses and mileage, approvers authorise claims, finance reviews them, and anyone can book accommodation, trains, ferries and flights, including for a colleague. Angular and TypeScript on the front end, Java EE and SQL behind it, and it was adopted across the whole company.',
            'link' => null,
        ]);

        // After Bird Box Shop (40) and before ASM Sim & Learn (50).
        DB::table('projects')->where('id', $project->id)->update(['sort_order' => 45]);

        $disk = Storage::disk('public');
        $source = resource_path('views/projects/projects/'.self::SLUG.'/main.png');
        $target = 'projects/'.self::SLUG.'/0.png';
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

        Cache::forget('nav.projects');
    }

    public function down(): void
    {
        // Not Project::delete(): that also deletes the write-up view from resources/.
        $id = DB::table('projects')->where('slug', self::SLUG)->value('id');
        if ($id) {
            DB::table('project_images')->where('project_id', $id)->delete();
            DB::table('projects')->where('id', $id)->delete();
            Storage::disk('public')->deleteDirectory('projects/'.self::SLUG);
        }

        Cache::forget('nav.projects');
    }
};
