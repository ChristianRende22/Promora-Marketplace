<?php

namespace App\Domain\PromoCode\Contracts;

use App\Domain\PromoCode\ValueObjects\OrderContext;

interface OrderableInterface
{
    public function getSubtotal(): float;

    public function getOrderContext(): OrderContext;
}
