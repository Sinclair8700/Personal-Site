<?php

use App\Models\Project;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Each project's write-up is resources/views/projects/projects/{slug}/index.blade.php
     * and its card image is main.png next to it, copied into public storage here.
     */
    private const PROJECTS = [
        [
            'slug' => 'bird-box-shop',
            'name' => 'Bird Box Shop',
            'description' => 'A WordPress and WooCommerce shop for a business selling timber bird boxes and garden planters. I designed a new block theme for it that also repaired around 200 broken colour references in the existing pages without editing any of them, and built a Docker copy of the live site with scripts that pull production down safely and deploy theme changes back up with a backup and rollback.',
            'link' => 'https://birdboxshop.co.uk',
        ],
        [
            'slug' => 'lil-chongee-bud-bro',
            'name' => 'Lil Chongee & Bud-Bro',
            'description' => 'A top-down 3D stoner comedy game with an 8-bit look, built in Godot 4. Keep your Sativa and Indica bars up, earn Vibes with your Bud Bros, and dodge Feds, K9 units and drones across eight districts to find The Plug, alone or in two-player online co-op. Around 32,000 lines of GDScript, music and sound synthesised at runtime, and CI that builds and tests Windows, macOS and Linux releases.',
            'link' => null,
        ],
        [
            'slug' => 'the-print-shop-tunstall',
            'name' => 'The Print Shop Tunstall',
            'description' => 'A from-scratch rebuild of the website for a printing, signage and workwear shop in Stoke-on-Trent, in Node.js, Express and SQLite. Customers can design sticker sheets and business cards in the browser and get print-ready files, pay by card, PayPal or bank transfer, and approve proofs online. The shop runs everything from its own admin: products, price lists, an orders board, proofs and email replies.',
            'link' => 'https://theprintshoptunstall.co.uk',
        ],
    ];

    public function up(): void
    {
        $disk = Storage::disk('public');

        foreach (self::PROJECTS as $data) {
            $project = Project::firstOrCreate(['slug' => $data['slug']], [
                'name' => $data['name'],
                'description' => $data['description'],
                'link' => $data['link'],
            ]);

            $source = resource_path('views/projects/projects/'.$data['slug'].'/main.png');
            $target = 'projects/'.$data['slug'].'/0.png';
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
        }

        Cache::forget('nav.projects');
    }

    public function down(): void
    {
        // Not Project::delete(): that also deletes the write-up views from resources/.
        foreach (self::PROJECTS as $data) {
            $id = DB::table('projects')->where('slug', $data['slug'])->value('id');
            if ($id) {
                DB::table('project_images')->where('project_id', $id)->delete();
                DB::table('projects')->where('id', $id)->delete();
                Storage::disk('public')->deleteDirectory('projects/'.$data['slug']);
            }
        }

        Cache::forget('nav.projects');
    }
};
