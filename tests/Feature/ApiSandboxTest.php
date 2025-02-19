<?php

use function Pest\Laravel\get;
use function Pest\Laravel\post;
use Illuminate\Support\Facades\Cache;

it('shows the api sandbox page', function () {
    get(route('api-sandbox.index'))
        ->assertOk();
});

function getApiSandboxToken(){
    $response = get(route('api-sandbox.index'));
    $response->assertOk();

    $session = session();
    $token = $session->get('api_sandbox_token');
    expect($token)->toHaveLength(16);
    expect(Cache::has('sandbox_token_' . $token))->toBeTrue();
    return $token;
}


it('shows a temporary api key on the api sandbox page', function () {
    $response = get(route('api-sandbox.index'));
    $token = getApiSandboxToken();
    // Assert the token is displayed on the page
    $response->assertSee($token);
});

it('provides an api key that can be used to make requests ', function () {

    $token = getApiSandboxToken();

    get(route('api-sandbox.api.message.index', ['token' => $token]))
        ->assertOk();
});

it('can store a message', function () {
    $token = getApiSandboxToken();

    $test_message = 'test message';

    post(route('api-sandbox.api.message.store', ['token' => $token, 'message' => $test_message]))
        ->assertOk();

    get(route('api-sandbox.api.message.index', ['token' => $token]))
        ->assertSee($test_message);
});

it('can store multiple messages', function () {
    $token = getApiSandboxToken();

    $test_messages = ['test message 1', 'test message 2', 'test message 3'];

    foreach ($test_messages as $message) {
        post(route('api-sandbox.api.message.store', ['token' => $token, 'message' => $message]))
            ->assertOk();
    }

    get(route('api-sandbox.api.message.index', ['token' => $token]))
        ->assertSeeInOrder($test_messages);
});

it('can show messages on the api sandbox page', function () {
    $token = getApiSandboxToken();

    $test_messages = ['test message 4', 'test message 5', 'test message 6'];

    foreach ($test_messages as $message) {
        post(route('api-sandbox.api.message.store', ['token' => $token, 'message' => $message]))
            ->assertOk();
    }

    get(route('api-sandbox.index'))
        ->assertSeeInOrder($test_messages);
});
