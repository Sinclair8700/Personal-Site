<?php

namespace App\Models;

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
        static::deleting(function (ProjectImage $image) {
            Storage::disk('public')->delete($image->path());
        });
    }
}
