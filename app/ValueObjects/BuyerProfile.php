<?php

declare(strict_types=1);

namespace App\ValueObjects;

use App\Contracts\BuyerProfileInterface;

/**
 * Value Object inmutable que representa la identidad y estado inicial del comprador en memoria.
 */
final readonly class BuyerProfile implements BuyerProfileInterface
{
    public function __construct(
        private string|int $id,
        private bool $isFirstOrder
    ) {
    }

    public function getId(): string|int
    {
        return $this->id;
    }

    public function isFirstOrder(): bool
    {
        return $this->isFirstOrder;
    }
}
