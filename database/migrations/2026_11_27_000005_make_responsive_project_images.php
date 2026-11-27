<?php

use App\Support\ResponsiveImage;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * WebP copies (480, 960 and 1440 wide) of every project image, so the home page carousel
     * and project pages stop sending 2 MB PNGs (PageSpeed: "Improve image delivery").
     * New uploads get theirs in ProjectImage::created. Skipped in tests: it's slow and the
     * views fall back to the original when there are no copies.
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
        DB::table('project_images')
            ->join('projects', 'projects.id', '=', 'project_images.project_id')
            ->get(['projects.slug', 'project_images.filename'])
            ->each(fn ($row) => ResponsiveImage::delete('projects/'.$row->slug.'/'.$row->filename));
    }
};
