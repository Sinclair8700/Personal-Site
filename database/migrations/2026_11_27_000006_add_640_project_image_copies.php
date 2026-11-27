<?php

use App\Support\ResponsiveImage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * WebP copies 640 wide of every project image (480, 960 and 1440 came in 000005),
     * the size phones pick on the home page carousel. generate() only makes missing copies.
     * Skipped in tests, like 000005.
     */
    public function up(): void
    {
        if (app()->runningUnitTests()) {
            return;
        }

        DB::table('project_images')
            ->join('projects', 'projects.id', '=', 'project_images.project_id')
            ->get(['projects.slug', 'project_images.filename'])
            ->each(fn ($row) => ResponsiveImage::generate('projects/'.$row->slug.'/'.$row->filename));
    }

    public function down(): void
    {
        // Nothing to undo: 000005's down() removes every copy.
    }
};
