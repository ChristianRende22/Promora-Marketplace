<?php

namespace App\Domain\PromoCode\ValueObjects;

readonly class OrderContext
{
    /**
     * @param array<int|string> $currentOrders Collection of order IDs currently in process to exclude from historical counts.
     */
    public function __construct(
        public string $buyerProfile,
        public int|string $categoryId,
        public array $currentOrders = []
    ) {
    }
}
