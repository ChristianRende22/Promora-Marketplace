<?php

namespace App\Rules\Configurable;

use App\Contracts\OrderableInterface;
use App\Contracts\RuleSpecificationInterface;
use App\ValueObjects\ValidationResult;

class FirstOrderOnlyRule implements RuleSpecificationInterface
{
    public function __construct()
    {
    }

    public function isSatisfiedBy(OrderableInterface $order): ValidationResult
    {
        $context = $order->getOrderContext();
        
        if (!$context->buyerProfile->isFirstOrder()) {
            return ValidationResult::failed('code_already_used');
        }

        return ValidationResult::success();
    }
}
