<?php

use function Pest\Laravel\get;

it('returns a successful response from home page', function () {
    get(route('index'))->assertOk();
});

it('returns a successful response from projects page', function () {
    get(route('projects.index'))->assertOk();
});

it('returns a successful response from api sandbox page', function () {
    get(route('api-sandbox.index'))->assertOk();
});
