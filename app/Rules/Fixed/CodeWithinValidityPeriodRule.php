<?php

declare(strict_types=1);

namespace App\Rules\Fixed;

use App\Contracts\FixedValidationRuleInterface;
use App\Entities\PromoCode;
use App\ValueObjects\ValidationResult;
use DateTimeImmutable;

class CodeWithinValidityPeriodRule implements FixedValidationRuleInterface
{
    public function isSatisfiedBy(?PromoCode $promoCode): ValidationResult
    {
        $now = new DateTimeImmutable();

        if ($promoCode->startsAt !== null && $now < $promoCode->startsAt) {
            return ValidationResult::failed('expired_coupon');
        }

        if ($promoCode->endsAt !== null && $now > $promoCode->endsAt) {
            return ValidationResult::failed('expired_coupon');
        }

        return ValidationResult::success();
    }
}
