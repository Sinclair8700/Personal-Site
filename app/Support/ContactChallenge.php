<?php

namespace App\Support;

use Illuminate\Support\Facades\Cache;

/**
 * Proof-of-work bot check for the contact form, in the style of ALTCHA.
 *
 * The server picks a secret number, hashes it with a random salt and signs the
 * hash. The browser has to find the number by brute force (resources/js/
 * contact-challenge.js, well under a second of SHA-256 on most devices) before
 * the form can be sent. Bots that post the form directly can't, and bots that
 * run the page pay for every attempt. A challenge expires, can't be sent within
 * a few seconds of the page loading, and can only be used once, so one solved
 * puzzle can't be replayed for a burst of messages. No third-party service or
 * cookie is involved.
 */
class ContactChallenge
{
    /** Upper bound for the secret number; on average the browser tries half of these. */
    public const MAX_NUMBER = 100_000;

    /** Sooner than this after the page loads, a person can't have filled the form in. */
    public const MIN_SECONDS = 3;

    public const TTL_SECONDS = 6 * 60 * 60;

    public const OK = 'ok';

    public const TOO_FAST = 'too_fast';

    public const EXPIRED = 'expired';

    public const INVALID = 'invalid';

    /**
     * @return array{salt: string, challenge: string, signature: string, max: int}
     */
    public static function issue(): array
    {
        // The issue time rides in the salt, so it is covered by the signed hash.
        $salt = bin2hex(random_bytes(12)).'.'.now()->timestamp.'.';
        $challenge = hash('sha256', $salt.random_int(0, self::MAX_NUMBER));

        return [
            'salt' => $salt,
            'challenge' => $challenge,
            'signature' => self::sign($challenge),
            'max' => self::MAX_NUMBER,
        ];
    }

    /**
     * Returns one of the OK, TOO_FAST, EXPIRED or INVALID constants. A valid
     * answer is used up, so the same challenge comes back INVALID next time.
     */
    public static function verify(mixed $salt, mixed $challenge, mixed $signature, mixed $number): string
    {
        if (! is_string($salt) || ! is_string($challenge) || ! is_string($signature) || ! is_string($number)
            || ! preg_match('/^[0-9a-f]{24}\.(\d{1,12})\.$/', $salt, $match)
            || ! preg_match('/^\d{1,7}$/', $number)) {
            return self::INVALID;
        }

        if (! hash_equals(self::sign($challenge), $signature)
            || ! hash_equals($challenge, hash('sha256', $salt.$number))) {
            return self::INVALID;
        }

        $age = now()->timestamp - (int) $match[1];

        if ($age > self::TTL_SECONDS) {
            return self::EXPIRED;
        }

        if ($age < self::MIN_SECONDS) {
            return self::TOO_FAST;
        }

        // Cache::add only succeeds for the first use of a challenge.
        if (! Cache::add('contact-challenge:'.$challenge, true, self::TTL_SECONDS)) {
            return self::INVALID;
        }

        return self::OK;
    }

    private static function sign(string $challenge): string
    {
        return hash_hmac('sha256', 'contact-challenge:'.$challenge, (string) config('app.key'));
    }
}
