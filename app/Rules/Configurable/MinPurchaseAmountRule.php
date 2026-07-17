<?php

namespace App\Rules\Configurable;

use App\Contracts\OrderableInterface;
use App\Contracts\RuleSpecificationInterface;
use App\ValueObjects\ValidationResult;

class MinPurchaseAmountRule implements RuleSpecificationInterface
{
    public function __construct(private readonly float $minAmount)
    {
    }

    public function isSatisfiedBy(OrderableInterface $order): ValidationResult
    {
        if ($order->getSubtotal() < $this->minAmount) {
            return ValidationResult::failed('min_amount_required');
        }

        return ValidationResult::success();
    }
}
