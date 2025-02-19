<?php

use App\Models\Project;
use function Pest\Laravel\get;
use Pest\Laravel\AssertableHtml;

it('shows projects on home page', function () {
    $projects = Project::all();
    foreach ($projects as $project) {
        $image = base_path('/resources/views/projects/projects/'.$project->slug.'/main.png');
        if(!file_exists($image)){
            continue;
        }

        get(route('index'))
            ->assertSee($project->name)
            ->assertSeeInOrder(['<h1', $project->name, '</h1>'], false);
    }
});

it('only shows projects with images', function () {
    $projects = Project::all();
    foreach ($projects as $project) {
        $image = base_path('/resources/views/projects/projects/'.$project->slug.'/main.png');
        if(file_exists($image)){
            continue;
        }

        try{
            get(route('index'))
                ->assertSeeInOrder(['<h1', $project->name, '</h1>'], false);

            $this->fail('Project '.$project->name.' should not be shown');
        } catch (Exception $e) {
            continue;
        }
    }
});
