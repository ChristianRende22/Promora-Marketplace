<?php

namespace App\Domain\PromoCode\Rules;

use App\Domain\PromoCode\Contracts\OrderableInterface;
use App\Domain\PromoCode\Contracts\PromoCodeRepositoryInterface;
use App\Domain\PromoCode\Contracts\RuleSpecificationInterface;
use App\Domain\PromoCode\Exceptions\RuleValidationException;

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
        $isAllowed = $this->repository->isUserInRestrictedList($this->promoCode, $context->buyerProfile);

        if (!$isAllowed) {
            throw new RuleValidationException('restricted_usage', 'This promo code is restricted and not assigned to the current user.');
        }

        return true;
    }
}
