<?php

use App\Models\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    private const SLUG = 'keele-book-store';

    // The write-up is resources/views/projects/projects/keele-book-store/index.blade.php
    // and its card image is main.png next to it, copied into public storage here.
    public function up(): void
    {
        $project = Project::firstOrCreate(['slug' => self::SLUG], [
            'name' => 'Book Store',
            'description' => 'A bookstore web app I built for my Advanced Web Technologies module at Keele University in 2022. Customers browse books by category and type, fill a cart and place orders once logged in, and managers get a live orders page where they filter orders and update their status. Built with React and jQuery on the front end and PHP and MySQL behind it, with prepared statements and validation in the browser and on the server.',
            'link' => null,
        ]);

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
