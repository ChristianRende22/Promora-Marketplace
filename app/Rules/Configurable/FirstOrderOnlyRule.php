<?php

namespace App\Rules\Configurable;

use App\Contracts\OrderableInterface;
use App\Contracts\RuleSpecificationInterface;
use App\Exceptions\RuleValidationException;

class FirstOrderOnlyRule implements RuleSpecificationInterface
{
    public function __construct()
    {
    }

    public function isSatisfiedBy(OrderableInterface $order): bool
    {
        $context = $order->getOrderContext();
        
        if (!$context->buyerProfile->isFirstOrder()) {
            throw new RuleValidationException('code_already_used', 'This promo code is valid for first-time orders only.');
        }

        return true;
    }
}
