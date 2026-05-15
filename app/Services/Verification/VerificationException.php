<?php

namespace App\Services\Verification;

use RuntimeException;

/**
 * Domain exception raised by the verification pipeline. Friendly message
 * is intended for end-user display; the underlying log/context is kept
 * out of the message and put into structured logs instead.
 */
class VerificationException extends RuntimeException
{
    public function __construct(string $message = '', protected string $userMessage = '', ?\Throwable $previous = null)
    {
        parent::__construct($message, 0, $previous);
    }

    public function userMessage(): string
    {
        return $this->userMessage !== '' ? $this->userMessage : $this->getMessage();
    }
}
