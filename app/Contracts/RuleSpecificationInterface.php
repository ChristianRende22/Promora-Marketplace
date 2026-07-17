<?php

namespace App\Contracts;

use App\Exceptions\RuleValidationException;

interface RuleSpecificationInterface
{
    /**
     * Evaluates the given order against the rule logic.
     * 
     * @param OrderableInterface $order
     * @return bool True if rule is satisfied
     * @throws RuleValidationException If the rule fails, exposing a semantic error code.
     */
    public function isSatisfiedBy(OrderableInterface $order): bool;
}
