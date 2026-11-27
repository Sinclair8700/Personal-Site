<?php

use App\Mail\NewContactMessage;
use App\Models\Contact;
use App\Support\ContactChallenge;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Mail;

use function Pest\Laravel\from;
use function Pest\Laravel\get;
use function Pest\Laravel\travel;

uses(RefreshDatabase::class);

/** Issue a challenge and solve it the way the browser does. */
function solvedChallenge(): array
{
    $c = ContactChallenge::issue();

    for ($n = 0; $n <= $c['max']; $n++) {
        if (hash('sha256', $c['salt'].$n) === $c['challenge']) {
            break;
        }
    }

    return [
        'challenge_salt' => $c['salt'],
        'challenge' => $c['challenge'],
        'challenge_signature' => $c['signature'],
        'challenge_number' => (string) $n,
    ];
}

function sendContact(array $fields)
{
    return from(route('contact.index'))->post(route('contact.store'), $fields + [
        'email_address' => 'someone@example.com',
        'message' => 'Hello Alex, I would like to talk about a project.',
    ]);
}

it('renders the bot check with Send disabled until it is solved', function () {
    get(route('contact.index'))
        ->assertOk()
        ->assertSee('name="challenge_salt"', false)
        ->assertSee('name="challenge_signature"', false)
        ->assertSee('data-challenge-submit', false)
        ->assertSee('disabled', false);
});

it('stores a message with a solved challenge', function () {
    $challenge = solvedChallenge();
    travel(5)->seconds();

    sendContact($challenge)->assertRedirect(route('contact.index'))->assertSessionHas('success');

    expect(Contact::count())->toBe(1);
});

it('silently drops honeypot, missing and forged challenges', function (Closure $fields) {
    $fields = $fields();
    travel(5)->seconds();

    sendContact($fields)->assertRedirect(route('contact.index'))->assertSessionHas('success');

    expect(Contact::count())->toBe(0);
})->with([
    'honeypot filled' => fn () => solvedChallenge() + ['website' => 'https://spam.example'],
    'no challenge' => fn () => [],
    'forged signature' => fn () => ['challenge_signature' => str_repeat('0', 64)] + solvedChallenge(),
    'wrong answer' => fn () => ['challenge_number' => '9999999'] + solvedChallenge(),
]);

it('only accepts each solved challenge once', function () {
    $challenge = solvedChallenge();
    travel(5)->seconds();

    sendContact($challenge);
    sendContact($challenge + ['message' => 'A second, different message from a bot.']);

    expect(Contact::count())->toBe(1);
});

it('asks people to retry when they send too soon or too late', function (int $seconds, string $error) {
    $challenge = solvedChallenge();
    travel($seconds)->seconds();

    sendContact($challenge)->assertRedirect(route('contact.index'))->assertSessionHasErrors(['challenge' => $error]);

    expect(Contact::count())->toBe(0);
})->with([
    'too fast' => [1, 'That was quick! Please wait a moment and press Send again.'],
    'expired' => [ContactChallenge::TTL_SECONDS + 60, 'This page was open too long, so the form expired. Please press Send again.'],
]);

it('rejects messages with more than two links', function () {
    $challenge = solvedChallenge();
    travel(5)->seconds();

    sendContact($challenge + ['message' => 'Buy now https://a.example https://b.example www.c.example'])
        ->assertSessionHasErrors('message');

    expect(Contact::count())->toBe(0);
});

it('ignores the same message sent again within a day', function () {
    $first = solvedChallenge();
    $second = solvedChallenge();
    travel(5)->seconds();

    sendContact($first)->assertSessionHas('success');
    sendContact($second)->assertSessionHas('success');

    expect(Contact::count())->toBe(1);
});

it('emails each new message to Alex with Reply-To set to the sender', function () {
    Mail::fake();
    $challenge = solvedChallenge();
    travel(5)->seconds();

    sendContact($challenge);

    Mail::assertSent(NewContactMessage::class, function (NewContactMessage $mail) {
        $mail->assertHasReplyTo('someone@example.com');
        $mail->assertSeeInText('Hello Alex, I would like to talk about a project.');

        return $mail->hasTo('me@alexdavi.es');
    });
});

it('does not email dropped spam or repeated messages', function () {
    Mail::fake();
    $first = solvedChallenge();
    $second = solvedChallenge();
    travel(5)->seconds();

    sendContact(['website' => 'https://spam.example'] + solvedChallenge());
    sendContact($first);
    sendContact($second);

    Mail::assertSentCount(1);
});

it('still saves the message when the email cannot be sent', function () {
    Mail::shouldReceive('to->send')->andThrow(new RuntimeException('mail server down'));
    $challenge = solvedChallenge();
    travel(5)->seconds();

    sendContact($challenge)->assertSessionHas('success');

    expect(Contact::count())->toBe(1);
});

it('rate limits each visitor to three sends in ten minutes', function () {
    foreach (range(1, 3) as $i) {
        sendContact([])->assertRedirect();
    }

    sendContact([])->assertStatus(429);
});
