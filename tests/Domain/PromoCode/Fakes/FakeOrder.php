<?php

namespace Tests\Domain\PromoCode\Fakes;

use App\Domain\PromoCode\Contracts\OrderableInterface;
use App\Domain\PromoCode\ValueObjects\OrderContext;

class FakeOrder implements OrderableInterface
{
    public function __construct(
        private readonly float $subtotal,
        private readonly OrderContext $orderContext
    ) {
    }

    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    public function getOrderContext(): OrderContext
    {
        return $this->orderContext;
    }
}
