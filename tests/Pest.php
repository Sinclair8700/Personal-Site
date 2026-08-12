<?php

use Illuminate\Foundation\Testing\RefreshDatabase;

/*
|--------------------------------------------------------------------------
| Test Case
|--------------------------------------------------------------------------
|
| Bind the application TestCase to every Feature test and refresh the
| database between tests. Migrations inline-seed the projects and education
| tables, so RefreshDatabase gives each test a clean, populated schema.
|
*/

uses(
    Tests\TestCase::class,
    RefreshDatabase::class,
)->in('Feature');
