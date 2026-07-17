<?php

namespace Tests\Fakes;

use App\Contracts\OrderableInterface;
use App\ValueObjects\OrderContext;

class FakeOrder implements OrderableInterface
{
    public function __construct(
        private readonly float $subtotal,
        private readonly OrderContext $orderContext,
        private readonly string|int $id = 'fake-order-id'
    ) {
    }

    public function getSubtotal(): float
    {
        return $this->subtotal;
    }

    public function getId(): string|int
    {
        return $this->id;
    }

    public function getOrderContext(): OrderContext
    {
        return $this->orderContext;
    }
}
