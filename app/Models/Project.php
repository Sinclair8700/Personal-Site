<?php

namespace App\Models;

use Illuminate\Support\HtmlString;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Storage;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'link'];

    public function getRouteKeyName(): string
    {
        return 'slug';
    }

    public static function boot()
    {
        parent::boot();
        
        static::created(function ($project) {
            // Create project directory in storage/app/public
            Storage::disk('public')->makeDirectory('projects/'.$project->slug);
            
            // Create index.blade.php in views directory
            $path = base_path('/resources/views/projects/projects/'.$project->slug);
            if(!file_exists($path.'/index.blade.php')){
                mkdir($path, 0755, true);
                $file = fopen($path.'/index.blade.php', 'w');
                fwrite($file, '<h1>Coming Soon</h1>');
                fclose($file);
            }
        });
    }

    public function delete()
    {
        // Delete project directory from storage/app/public
        Storage::disk('public')->deleteDirectory('projects/'.$this->slug);
        
        // Delete project view directory
        $path = base_path('/resources/views/projects/projects/'.$this->slug);
        
        if(file_exists($path.'/index.blade.php')){
            unlink($path.'/index.blade.php');
        }
        
        if(file_exists($path)){
            rmdir($path);
        }

        parent::delete();
    }

    public function name() : Attribute
    {
        return Attribute::make(
            fn() => new HtmlString(e($this->getRawOriginal('name') ?? 'No name'))
        );
    }

    public function description() : Attribute
    {
        return Attribute::make(
            fn() => new HtmlString(e($this->getRawOriginal('description') ?? 'No description'))
        );
    }

    public function images()
    {
        return $this->hasMany(ProjectImage::class)->orderBy('sort_order');
    }

    /**
     * Get the primary/first image (for backward compatibility and card display).
     */
    public function primaryImage(): ?ProjectImage
    {
        return $this->images()->first();
    }

    /**
     * Check if the project has any images.
     */
    public function hasImages(): bool
    {
        return $this->images()->exists();
    }

    /**
     * Add uploaded images to the project. Priority is upload order (0, 1, 2...).
     */
    public function addImages(array $files): void
    {
        $nextOrder = $this->images()->max('sort_order') ?? -1;

        foreach ($files as $file) {
            if (!$file->isValid()) {
                continue;
            }
            $nextOrder++;
            $extension = $file->getClientOriginalExtension() ?: $file->guessExtension() ?: 'png';
            $filename = $nextOrder . '.' . strtolower($extension);

            $file->storeAs('projects/' . $this->slug, $filename, 'public');

            ProjectImage::create([
                'project_id' => $this->id,
                'filename' => $filename,
                'sort_order' => $nextOrder,
            ]);
        }
    }
}
