<?php

namespace Database\Factories;

use App\Models\Project;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;
/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Project>
 */
class ProjectFactory extends Factory
{

    protected $model = Project::class;

    public function configure()
    {
        return $this->afterCreating(function (Project $project) {
            $path = base_path('/resources/views/projects/projects/'.$project->slug);
            
            if(!file_exists($path.'/index.blade.php')){
                mkdir($path, 0755, true);
                $file = fopen($path.'/index.blade.php', 'w');
                fwrite($file, '<h1>Coming Soon</h1>');
                fclose($file);
            }
        });
    }

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {

        $name = fake()->name(); 
        $slug = Str::slug($name);
        $description = fake()->sentence();
        $link = fake()->url();

        return [
            'name' => $name,
            'slug' => $slug,
            'description' => $description,
            'link' => $link
            //
        ];
    }
}
