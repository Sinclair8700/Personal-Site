<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Project;
class ProjectSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        
        $projects = [
            [
                'name' => 'ASM Sim & Learn',
                'slug' => 'assembly-simulator',
                'description' => 'ASM Sim & Learn',
                'github_link' => 'https://github.com/project1',
            ],
            [
                'name' => 'Pool & Snooker Simulator',
                'slug' => 'pool-snooker-simulator',
                'description' => 'Pool & Snooker Simulator',
                'github_link' => 'https://github.com/project1',
            ],
            [
                'name' => 'Basic C++ Game Engine',
                'slug' => 'basic-c-game-engine',
                'description' => 'Basic C++ Game Engine',
                'github_link' => 'https://github.com/project1',
            ],
            [
                'name' => 'Fibre Maps',
                'slug' => 'fibre-maps',
                'description' => 'Fibre Maps',
                'github_link' => 'https://github.com/project1',
            ],
            [
                'name' => 'GPT Discord Bot',
                'slug' => 'gpt-discord-bot',
                'description' => 'GPT Discord Bot',
                'github_link' => 'https://github.com/project1',
            ],
            [
                'name' => 'DevSpeedup',
                'slug' => 'devspeedup',
                'description' => 'DevSpeedup',
                'github_link' => 'https://github.com/project1',
            ],
            [
                'name' => 'Music 2',
                'slug' => 'music-2',
                'description' => 'Music 2',
                'github_link' => 'https://github.com/project1',
            ],
            [
                'name' => 'Chess Game',
                'slug' => 'chess-game',
                'description' => 'Chess Game',
                'github_link' => '',
            ],
            [
                'name' => 'Sim Shifter',
                'slug' => 'sim-shifter',
                'description' => 'Sim Shifter',
                'github_link' => '',
            ],
            [
                'name' => 'Co-production Game',
                'slug' => 'co-production-game',
                'description' => 'Co-production game',
                'github_link' => '',
            ],
            [
                'name' => 'Old Site',
                'slug' => 'old-site',
                'description' => 'Old Site',
                'github_link' => '',
            ],

        ];
        Project::insert($projects);
        foreach($projects as $project){
            $path = base_path('/resources/views/projects/projects/'.$project['slug']);
            
            if(file_exists($path.'/index.blade.php')){
                continue;
            }
            
            // Create directory structure if it doesn't exist
            if (!file_exists($path)) {
                mkdir($path, 0755, true);
            }
            
            // make the index.blade.php file
            $file = fopen($path.'/index.blade.php', 'w');
            fwrite($file, '<h1>Coming Soon</h1>');
            fclose($file);
        }
    }
}
