<?php

use App\Models\Education;
use Illuminate\Support\Facades\Storage;
use function Pest\Laravel\get;

it('shows education on education index page', function () {
    $education = Education::all();
    foreach ($education as $education) {
        get(route('education.index'))
            ->assertSeeInOrder(['<h2', $education->name, '</h2>'], false);
    }
});

it('shows project details on projects index page', function () {
    $education = Education::all();
    foreach ($education as $education) {
        get(route('education.index'))
            ->assertSeeInOrder(['<p', $education->description, '</p>'], false);
    }
});

it('shows education images on education index page', function () {
    Storage::fake('public');
    $education = Education::all();
    foreach ($education as $education) {
        Storage::disk('public')->put('education/'.$education->image, 'image');
        get(route('education.index'))
            ->assertSeeInOrder(['<a', '<img', $education->image, '</a>'], false);
    }
});

it('shows the placeholder instead of a missing education image', function () {
    Storage::fake('public');

    get(route('education.index'))
        ->assertSee('images/education-placeholder.png', false)
        ->assertDontSee('storage/education/', false);
});

it('shows the education title on the education show page', function () {
    $education = Education::all();
    foreach ($education as $education) {
        get(route('education.show', $education->slug))
            ->assertSeeInOrder(['<h1', $education->name, '</h1>'], false);
    }
});

it('shows all of the education in the education dropdown within the header', function () {
    $education = Education::all();
    foreach ($education as $edu) { 
        get(route('education.index'))
            ->assertSeeInOrder(['<a', $edu->name, '</a>'], false);
    }
});