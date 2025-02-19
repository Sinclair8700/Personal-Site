<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    /** @use HasFactory<\Database\Factories\ProjectFactory> */
    use HasFactory;

    protected $fillable = ['name', 'slug', 'description', 'github_link'];

    public function delete()
    {
        $path = base_path('/resources/views/projects/projects/'.$this->slug);

        if(file_exists($path.'/index.blade.php')){
            unlink($path.'/index.blade.php');
        }

        if(file_exists($path.'/main.png')){
            unlink($path.'/main.png');
        }

        if(file_exists($path)){
            rmdir($path);
        }

        parent::delete();
    }
}
