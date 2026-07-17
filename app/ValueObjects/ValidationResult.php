<?php

declare(strict_types=1);

namespace App\ValueObjects;

final readonly class ValidationResult
{
    private function __construct(
        public bool $isValid,
        public ?string $errorCode = null
    ) {
    }

    public static function success(): self
    {
        return new self(true);
    }

    public static function failed(string $errorCode): self
    {
        return new self(false, $errorCode);
    }
}
