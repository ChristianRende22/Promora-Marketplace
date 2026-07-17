<?php

declare(strict_types=1);

namespace App\Engine;

use App\Contracts\OrderableInterface;
use App\Contracts\RuleSpecificationInterface;
use App\ValueObjects\ValidationResult;

/**
 * Evalúa la colección de reglas configurables (Specifications) que
 * PromoCodeRuleFactory construyó para un código, en orden, cortando en la
 * primera que falle. Las 3 reglas fijas (existencia, vigencia, estado activo)
 * las corre el caso de uso directamente contra FixedRuleChain, antes de
 * construir estas Specifications — el Engine nunca conoce el PromoCode.
 */
final class PromoCodeEngine
{
    /**
     * @param iterable<RuleSpecificationInterface> $configurableRules
     */
    public function __construct(private readonly iterable $configurableRules)
    {
    }

    public function validate(OrderableInterface $order): ValidationResult
    {
        foreach ($this->configurableRules as $rule) {
            $result = $rule->isSatisfiedBy($order);

            if (!$result->isValid) {
                return $result;
            }
        }

        return ValidationResult::success();
    }
}
