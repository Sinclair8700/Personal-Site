<?php

use App\Models\Project;
use function Pest\Laravel\get;

it('can create a project', function () {
    $project = Project::factory()->create();
    expect($project)->toBeInstanceOf(Project::class);


    $path = base_path('/resources/views/projects/projects/'.$project->slug);
    expect(file_exists($path.'/index.blade.php'))->toBeTrue();

    // delete the project from the database
    $project->delete();

    expect(file_exists($path))->toBeFalse();
});


it('shows projects on projects index page', function () {
    $projects = Project::all();
    foreach ($projects as $project) {
        get(route('projects.index'))
            ->assertSeeInOrder(['<h2', $project->name, '</h2>'], false);
    }
});

it('shows project details on projects index page', function () {
    $projects = Project::all();
    foreach ($projects as $project) {
        get(route('projects.index'))
            ->assertSeeInOrder(['<p', $project->description, '</p>'], false);
    }
});

it('shows project images on projects index page', function () {
    $projects = Project::all();
    foreach ($projects as $project) {
        get(route('projects.index'))
            ->assertSeeInOrder(['<a', $project->slug, '<img', '</a>'], false);
    }
});

it('shows the project title on the projects show page', function () {
    $projects = Project::all();
    foreach ($projects as $project) {
        get(route('projects.show', $project->slug))
            ->assertSeeInOrder(['<h1', $project->name, '</h1>'], false);
    }
});

it('shows all of the projects in the projects dropdown within the header', function () {
    $projects = Project::all();
    foreach ($projects as $project) {
        get(route('projects.index'))
            ->assertSeeInOrder(['<a', $project->name, '</a>'], false);
    }
});




