<?php

declare(strict_types=1);

namespace App\Rules\Fixed;

use App\Contracts\FixedValidationRuleInterface;
use App\Entities\PromoCode;
use App\Exceptions\RuleValidationException;

class CodeExistsRule implements FixedValidationRuleInterface
{
    public function isSatisfiedBy(?PromoCode $promoCode): bool
    {
        if ($promoCode === null) {
            throw new RuleValidationException(
                'invalid_code',
                'The promo code does not exist.'
            );
        }

        return true;
    }
}
