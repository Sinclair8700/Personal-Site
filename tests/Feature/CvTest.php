<?php

use App\Models\Cv;
use App\Models\User;

it('shows the CV without the phone number to visitors', function () {
    $this->get('/cv')
        ->assertOk()
        ->assertSee('Alex Davies')
        ->assertSee('Designamite Limited')
        ->assertSee('Save as PDF')
        ->assertDontSee('07534 708314')
        ->assertDontSee(route('cv.edit'));
});

it('shows the phone number and edit link to an admin', function () {
    $this->actingAs(User::factory()->admin()->create())
        ->get('/cv')
        ->assertOk()
        ->assertSee('07534 708314')
        ->assertSee(route('cv.edit'));
});

it('downloads the CV as Markdown, leaving the phone out for visitors', function () {
    $response = $this->get('/cv/download.md')->assertOk();

    expect($response->headers->get('content-disposition'))->toContain('alex-davies-cv.md')
        ->and($response->getContent())->toContain('# Alex Davies')
        ->and($response->getContent())->toContain('## Experience')
        ->and($response->getContent())->not->toContain('07534 708314');
});

it('keeps the editor to admins', function () {
    $this->get('/cv/edit')->assertRedirect();

    $this->actingAs(User::factory()->create())->get('/cv/edit')->assertForbidden();
    $this->actingAs(User::factory()->create())->put('/cv', ['name' => 'Someone'])->assertForbidden();

    $this->actingAs(User::factory()->admin()->create())->get('/cv/edit')->assertOk()->assertSee('Save CV');
});

it('lets an admin update the CV', function () {
    $cv = Cv::current();
    $data = array_merge($cv->data, [
        'headline' => 'Lead developer',
        'skills' => "Languages: PHP, Rust\nCloud: Azure (App Service, Key Vault)",
        'show_phone' => '1',
    ]);

    $this->actingAs(User::factory()->admin()->create())
        ->put('/cv', $data)
        ->assertRedirect(route('cv.edit'));

    $fresh = Cv::current();
    expect($fresh->field('headline'))->toBe('Lead developer')
        ->and($fresh->field('show_phone'))->toBeTrue()
        ->and($fresh->skillGroups()[1]['items'])->toBe(['Azure (App Service, Key Vault)']);

    auth()->logout();
    $this->get('/cv')->assertSee('Lead developer')->assertSee('07534 708314');
});

it('splits sections into entries with titles, organisations and dates', function () {
    $experience = Cv::current()->entries('experience');

    expect($experience['entries'][0])
        ->title->toBe('Full-stack Developer')
        ->org->toBe('Designamite Limited')
        ->meta->toBe('October 2022 to present');
});
