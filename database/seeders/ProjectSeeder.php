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
                'description' => 'A browser based 16 bit computer simulator which allows you to learn, write, assemble, run and share assembly code. I built this project as part of my university thesis on teaching people how to code in assembly language.',
                'github_link' => 'https://github.com/project1',
            ],
            [
                'name' => 'Pool & Snooker Simulator',
                'slug' => 'pool-snooker-simulator',
                'description' => 'A cross platform pool & snooker simulator game with togglable rules and custom game modes. I built this project as a part of my games computing course at university. It uses openframeworks for the graphics and open dynamics engine for physics.',
                'github_link' => 'https://github.com/project1',
            ],
            [
                'name' => 'Basic C++ Game Engine',
                'slug' => 'basic-c-game-engine',
                'description' => 'This project is an adaptation of the pool & snooker simulator but adjusted to allow for fast & simple cross platform c++ game development without the need for learning the complexities of openframeworks & open dynamics engine.',
                'github_link' => 'https://github.com/project1',
            ],
            [
                'name' => 'Fibre Maps',
                'slug' => 'fibre-maps',
                'description' => 'Fibre Maps was a project I built for the fibre company Fibrenest. It was a web app that allowed the company to see their network of fibreoptic cables from a web browser. Built with google maps api, javascript, php and postgresql.',
                'github_link' => 'https://github.com/project1',
            ],
            [
                'name' => 'GPT Discord Bot',
                'slug' => 'gpt-discord-bot',
                'description' => 'This is a discord bot that uses the openai api to generate text responses to messages in a discord server based on user defined personas as previous user messages. It is built with discord.py and the openai api.',
                'github_link' => 'https://github.com/project1',
            ],
            [
                'name' => 'DevSpeedup',
                'slug' => 'devspeedup',
                'description' => 'DevSpeedup is a C++ application built with ImGui that original was created for switching php versions in the devilbox docker container. It was then expanded to allow for generation of scripts from templates, saving & tagging useful commands and text, and tracking lines committed to git as well as uncommitted repository changes.',
                'github_link' => 'https://github.com/project1',
            ],
            [
                'name' => 'Music 2',
                'slug' => 'music-2',
                'description' => 'Music 2 is a music creation tool built in python which turns your keyboard into a sequencer with a number of effects. It is built with the pygame library for drawing.',
                'github_link' => 'https://github.com/project1',
            ],
            [
                'name' => 'Chess Game',
                'slug' => 'chess-game',
                'description' => 'A super simple c++ chess game built with open frameworks. It has the basic chess moves built in but no en passant, castling or promotion.',
                'github_link' => '',
            ],
            [
                'name' => 'Sim Shifter',
                'slug' => 'sim-shifter',
                'description' => 'This was a project that I started due to needing a gear shifter for my racing simulator and also to learn CAD in fusion 360. I built a to scale prototype out of cardboard but never finished the project due to lack of tools to build it.',
                'github_link' => '',
            ],
            [
                'name' => 'Co-production Game',
                'slug' => 'co-production-game',
                'description' => 'A game created by Hannah Mumby which aids in collaboration on projects. Only the website was built by myself using preact and tailwindcss.',
                'github_link' => '',
            ],
            [
                'name' => 'Old Site',
                'slug' => 'old-site',
                'description' => 'This is my old website that was created as a bit of fun and to get a grasp of animation within css. It is built with html, css and javascript.',
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
