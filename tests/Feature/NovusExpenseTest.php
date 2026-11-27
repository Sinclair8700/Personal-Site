<?php

use App\Models\Project;

it('lists Novus Expense after Bird Box Shop with its write-up and image', function () {
    $this->get('/projects/novus-expense')
        ->assertOk()
        ->assertSee('Novus Expense')
        ->assertSee('adopted across the whole company')
        // 0.png, or its WebP copies (0-480w.webp...) when they've been generated.
        ->assertSee('storage/projects/novus-expense/0');

    $order = Project::navList()->pluck('slug')->all();
    expect(array_search('novus-expense', $order))->toBe(array_search('bird-box-shop', $order) + 1);
});
