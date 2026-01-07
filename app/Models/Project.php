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

    public function escapedDescription() 
    {
        return Attribute::get(fn() => new HtmlString(nl2br(e($this->description))));
    }
}
