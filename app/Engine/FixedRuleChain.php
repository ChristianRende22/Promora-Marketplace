<?php

declare(strict_types=1);

namespace App\Engine;

use App\Contracts\FixedValidationRuleInterface;
use App\Entities\PromoCode;
use App\ValueObjects\ValidationResult;

/**
 * Chain of Responsibility (TDR sección 2): ejecuta las 3 reglas fijas en el orden
 * estricto en que fueron recibidas y corta en la primera que falle (RF-02),
 * devolviendo su ValidationResult. No decide el orden ni instancia las reglas:
 * eso lo define quien construye el motor.
 */
final class FixedRuleChain
{
    /**
     * @param FixedValidationRuleInterface[] $rules
     */
    public function __construct(private readonly array $rules)
    {
    }

    public function run(?PromoCode $promoCode): ValidationResult
    {
        foreach ($this->rules as $rule) {
            $result = $rule->isSatisfiedBy($promoCode);

            if (!$result->isValid) {
                return $result;
            }
        }

        return ValidationResult::success();
    }
}
