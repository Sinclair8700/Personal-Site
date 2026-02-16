<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

return new class extends Migration
{
    /**
     * Migrate existing main.png files to the project_images table.
     * Renames main.png to 0.png for consistent naming with new uploads.
     */
    public function up(): void
    {
        $disk = Storage::disk('public');

        foreach (DB::table('projects')->get() as $project) {
            $oldPath = 'projects/' . $project->slug . '/main.png';

            if (!$disk->exists($oldPath)) {
                continue;
            }

            $extension = pathinfo($oldPath, PATHINFO_EXTENSION);
            $newFilename = '0.' . $extension;
            $newPath = 'projects/' . $project->slug . '/' . $newFilename;

            if ($oldPath !== $newPath) {
                $disk->move($oldPath, $newPath);
            }

            DB::table('project_images')->insert([
                'project_id' => $project->id,
                'filename' => $newFilename,
                'sort_order' => 0,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $disk = Storage::disk('public');
        $migratedFilenames = ['0.png', '0.jpg', '0.jpeg', '0.gif', '0.svg'];

        foreach (DB::table('project_images')->whereIn('filename', $migratedFilenames)->get() as $image) {
            $slug = DB::table('projects')->where('id', $image->project_id)->value('slug');
            $path = 'projects/' . $slug . '/' . $image->filename;

            if ($disk->exists($path)) {
                $disk->move($path, 'projects/' . $slug . '/main.png');
            }

            DB::table('project_images')->where('id', $image->id)->delete();
        }
    }
};
