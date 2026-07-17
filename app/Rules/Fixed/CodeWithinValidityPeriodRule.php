<?php

declare(strict_types=1);

namespace App\Rules\Fixed;

use App\Contracts\FixedValidationRuleInterface;
use App\Entities\PromoCode;
use App\Exceptions\RuleValidationException;
use DateTimeImmutable;

class CodeWithinValidityPeriodRule implements FixedValidationRuleInterface
{
    public function isSatisfiedBy(?PromoCode $promoCode): bool
    {
        $now = new DateTimeImmutable();

        if ($promoCode->startsAt !== null && $now < $promoCode->startsAt) {
            throw new RuleValidationException(
                'expired_coupon',
                'The promo code is not within its validity period yet.'
            );
        }

        if ($promoCode->endsAt !== null && $now > $promoCode->endsAt) {
            throw new RuleValidationException(
                'expired_coupon',
                'The promo code validity period has already ended.'
            );
        }

        return true;
    }
}
