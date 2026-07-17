<?php

namespace App\Rules\Configurable;

use App\Contracts\OrderableInterface;
use App\Contracts\PromoCodeRepositoryInterface;
use App\Contracts\RuleSpecificationInterface;
use App\Exceptions\RuleValidationException;

class RestrictedUsageRule implements RuleSpecificationInterface
{
    public function __construct(
        private readonly string $promoCode,
        private readonly PromoCodeRepositoryInterface $repository
    ) {
    }

    public function isSatisfiedBy(OrderableInterface $order): bool
    {
        $context = $order->getOrderContext();
        $isAllowed = $this->repository->isUserInRestrictedList($this->promoCode, $context->buyerProfile->getId());

        if (!$isAllowed) {
            throw new RuleValidationException('restricted_usage', 'This promo code is restricted and not assigned to the current user.');
        }

        return true;
    }
}
