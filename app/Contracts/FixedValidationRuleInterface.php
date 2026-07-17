<?php

declare(strict_types=1);

namespace App\Contracts;

use App\Entities\PromoCode;
use App\ValueObjects\ValidationResult;

/**
 * Contrato de las 3 reglas fijas (TDR sección 2). A diferencia de RuleSpecificationInterface
 * (que evalúa la orden), las reglas fijas evalúan el propio código promocional:
 * su existencia, vigencia y estado. $promoCode es nullable porque la primera regla
 * fija (CodeExistsRule) es justamente la que valida que no sea null.
 */
interface FixedValidationRuleInterface
{
    public function isSatisfiedBy(?PromoCode $promoCode): ValidationResult;
}
