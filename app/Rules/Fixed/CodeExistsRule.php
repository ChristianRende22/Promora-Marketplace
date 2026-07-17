<?php

declare(strict_types=1);

namespace App\Rules\Fixed;

use App\Contracts\FixedValidationRuleInterface;
use App\Entities\PromoCode;
use App\ValueObjects\ValidationResult;

class CodeExistsRule implements FixedValidationRuleInterface
{
    public function isSatisfiedBy(?PromoCode $promoCode): ValidationResult
    {
        if ($promoCode === null) {
            return ValidationResult::failed('invalid_code');
        }

        return ValidationResult::success();
    }
}
