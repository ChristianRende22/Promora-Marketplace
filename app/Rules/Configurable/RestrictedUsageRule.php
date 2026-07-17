<?php

namespace App\Rules\Configurable;

use App\Contracts\OrderableInterface;
use App\Contracts\PromoCodeRepositoryInterface;
use App\Contracts\RuleSpecificationInterface;
use App\ValueObjects\ValidationResult;

class RestrictedUsageRule implements RuleSpecificationInterface
{
    public function __construct(
        private readonly string $promoCode,
        private readonly PromoCodeRepositoryInterface $repository
    ) {
    }

    public function isSatisfiedBy(OrderableInterface $order): ValidationResult
    {
        $context = $order->getOrderContext();
        $isAllowed = $this->repository->isUserInRestrictedList($this->promoCode, $context->buyerProfile->getId());

        if (!$isAllowed) {
            return ValidationResult::failed('restricted_usage');
        }

        return ValidationResult::success();
    }
}
