<?php

namespace App\Contracts;

use App\ValueObjects\ValidationResult;

interface RuleSpecificationInterface
{
    /**
     * Evaluates the given order against the rule logic.
     * 
     * @param OrderableInterface $order
     * @return ValidationResult
     */
    public function isSatisfiedBy(OrderableInterface $order): ValidationResult;
}
