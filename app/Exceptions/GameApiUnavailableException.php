<?php

namespace App\Exceptions;

use Exception;
use Throwable;

/**
 * Thrown when the external game API (Twitch/IGDB) is not configured
 * or cannot be reached, so the caller can degrade gracefully
 * instead of bubbling a 500 error.
 */
class GameApiUnavailableException extends Exception
{
    public static function notConfigured(): self
    {
        return new self('The game API is not configured. Check TWITCH_AUTH, TWITCH_CLIENT_ID, TWITCH_CLIENT_SECRET and GAME_API_URL.');
    }

    public static function upstreamFailure(string $message, ?Throwable $previous = null): self
    {
        return new self($message, 0, $previous);
    }

    public static function invalidResponse(): self
    {
        return new self('The game API returned an unexpected or empty response.');
    }
}
