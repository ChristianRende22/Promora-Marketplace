<?php

declare(strict_types=1);

namespace App\Rules\Fixed;

use App\Contracts\FixedValidationRuleInterface;
use App\Entities\PromoCode;
use App\Enums\PromoCodeStatus;
use App\Exceptions\RuleValidationException;

class CodeIsActiveRule implements FixedValidationRuleInterface
{
    public function isSatisfiedBy(?PromoCode $promoCode): bool
    {
        if ($promoCode->status !== PromoCodeStatus::Active) {
            throw new RuleValidationException(
                'invalid_code',
                'The promo code is not active.'
            );
        }

        return true;
    }
}
