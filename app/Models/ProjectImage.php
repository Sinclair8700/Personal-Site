<?php

namespace App\Models;

use App\Support\ResponsiveImage;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;

class ProjectImage extends Model
{
    protected $fillable = ['project_id', 'filename', 'sort_order'];

    public function project()
    {
        return $this->belongsTo(Project::class);
    }

    public function url(): string
    {
        return asset('storage/projects/' . $this->project->slug . '/' . $this->filename);
    }

    public function path(): string
    {
        return 'projects/' . $this->project->slug . '/' . $this->filename;
    }

    protected static function booted()
    {
        // Resized WebP copies for srcset (see ResponsiveImage), made once on upload.
        static::created(function (ProjectImage $image) {
            ResponsiveImage::generate($image->path());
        });

        static::deleting(function (ProjectImage $image) {
            ResponsiveImage::delete($image->path());
            Storage::disk('public')->delete($image->path());
        });
    }
}
