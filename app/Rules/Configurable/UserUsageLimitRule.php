<?php

namespace App\Rules\Configurable;

use App\Contracts\OrderableInterface;
use App\Contracts\PromoCodeRepositoryInterface;
use App\Contracts\RuleSpecificationInterface;
use App\Exceptions\RuleValidationException;

class UserUsageLimitRule implements RuleSpecificationInterface
{
    public function __construct(
        private readonly string $promoCode,
        private readonly int $maxUses,
        private readonly PromoCodeRepositoryInterface $repository
    ) {
    }

    public function isSatisfiedBy(OrderableInterface $order): bool
    {
        $context = $order->getOrderContext();
        $currentUses = $this->repository->getUserUsageCount($this->promoCode, $context->buyerProfile->getId(), $context->currentOrders);

        if ($currentUses >= $this->maxUses) {
            throw new RuleValidationException('usage_limit_reached', 'The user has reached the maximum usage limit for this promo code.');
        }

        return true;
    }
}
