<?php

declare(strict_types=1);

namespace App\Engine;

use App\Contracts\OrderableInterface;
use App\Contracts\RuleSpecificationInterface;
use App\Entities\PromoCode;

/**
 * Orquestador del motor (TDR): valida si un cupón es elegible para una orden.
 * Corre primero las 3 reglas fijas (FixedRuleChain, orden estricto) y, solo si
 * pasan, las reglas configurables que Persona D construyó vía su Factory Method
 * y entrega ya listas por constructor. El Engine nunca instancia ninguna regla.
 */
final class PromoCodeEngine
{
    /**
     * @param iterable<RuleSpecificationInterface> $configurableRules
     */
    public function __construct(
        private readonly FixedRuleChain $fixedRules,
        private readonly iterable $configurableRules
    ) {
    }

    public function validate(?PromoCode $promoCode, OrderableInterface $order): void
    {
        $this->fixedRules->run($promoCode);

        foreach ($this->configurableRules as $rule) {
            $rule->isSatisfiedBy($order);
        }
    }
}
