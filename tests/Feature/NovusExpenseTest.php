<?php

use App\Models\Project;

it('lists Novus Expense after Bird Box Shop with its write-up and image', function () {
    $this->get('/projects/novus-expense')
        ->assertOk()
        ->assertSee('Novus Expense')
        ->assertSee('adopted across the whole company')
        ->assertSee('storage/projects/novus-expense/0.png');

    $order = Project::navList()->pluck('slug')->all();
    expect(array_search('novus-expense', $order))->toBe(array_search('bird-box-shop', $order) + 1);
});
