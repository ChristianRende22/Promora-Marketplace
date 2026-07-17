<?php

declare(strict_types=1);

namespace App\Rules\Fixed;

use App\Contracts\FixedValidationRuleInterface;
use App\Entities\PromoCode;
use App\Enums\PromoCodeStatus;
use App\ValueObjects\ValidationResult;

class CodeIsActiveRule implements FixedValidationRuleInterface
{
    public function isSatisfiedBy(?PromoCode $promoCode): ValidationResult
    {
        if ($promoCode->status !== PromoCodeStatus::Active) {
            return ValidationResult::failed('invalid_code');
        }

        return ValidationResult::success();
    }
}
