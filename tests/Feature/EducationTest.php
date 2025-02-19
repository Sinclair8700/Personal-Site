<?php

use App\Models\Education;

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
    $education = Education::all();
    foreach ($education as $education) {
        get(route('education.index'))
            ->assertSeeInOrder(['<a', $education->slug, '<img', '</a>'], false);
    }
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
        echo $edu->name;
        get(route('education.index'))
            ->assertSeeInOrder(['<a', $edu->name, '</a>'], false);
    }
});