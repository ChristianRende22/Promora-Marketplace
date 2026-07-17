<?php

namespace App\Rules\Configurable;

use App\Contracts\OrderableInterface;
use App\Contracts\RuleSpecificationInterface;
use App\Exceptions\RuleValidationException;

class MinPurchaseAmountRule implements RuleSpecificationInterface
{
    public function __construct(private readonly float $minAmount)
    {
    }

    public function isSatisfiedBy(OrderableInterface $order): bool
    {
        if ($order->getSubtotal() < $this->minAmount) {
            throw new RuleValidationException(
                'min_amount_required', 
                'The order subtotal does not meet the minimum required amount for this promo code.'
            );
        }

        return true;
    }
}
