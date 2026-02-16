<?php

use App\Models\Project;
use function Pest\Laravel\get;

it('shows projects on home page', function () {
    $projects = Project::with('images')->get();
    foreach ($projects as $project) {
        if (!$project->hasImages()) {
            continue;
        }

        get(route('index'))
            ->assertSee($project->name)
            ->assertSeeInOrder(['<h1', $project->name, '</h1>'], false);
    }
});

it('only shows projects with images', function () {
    $projects = Project::with('images')->get();
    foreach ($projects as $project) {
        if ($project->hasImages()) {
            continue;
        }

        try {
            get(route('index'))
                ->assertSeeInOrder(['<h1', $project->name, '</h1>'], false);

            $this->fail('Project ' . $project->name . ' should not be shown');
        } catch (Exception $e) {
            continue;
        }
    }
});
