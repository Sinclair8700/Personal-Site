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

it('includes the details from the older CVs', function () {
    $cv = Cv::current();

    expect($cv->field('linkedin'))->toContain('linkedin.com/in/')
        ->and($cv->field('experience'))->toContain('Azure Active Directory')
        ->and($cv->field('experience'))->toContain('adopted across the whole company')
        ->and($cv->field('education'))->toContain('Robot Wars')
        ->and($cv->field('interests'))->toContain('IT Ambassador')
        ->and(collect($cv->skillGroups())->firstWhere('area', 'Data')['items'])->toContain('MSSQL');

    $this->get('/cv')->assertOk()->assertSee('LinkedIn')->assertSee('I started coding at 12');

    // Running the patch again changes nothing.
    $before = Cv::current()->data;
    (require database_path('migrations/2026_11_27_000002_add_older_cv_details_to_cv.php'))->up();
    expect(Cv::current()->data)->toBe($before);
});

it('lists Novus Expense among the CV projects, after ASM Sim & Learn', function () {
    $titles = array_column(Cv::current()->entries('projects', false)['entries'], 'title');
    $novus = array_search('[Novus Expense](https://alex-davies.com/projects/novus-expense)', $titles);

    expect($novus)->not->toBeFalse()
        ->and($titles[$novus - 1])->toContain('ASM Sim & Learn');

    $this->get('/cv')->assertSee('Novus Expense');
});
