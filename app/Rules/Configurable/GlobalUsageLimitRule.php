<?php

namespace App\Rules\Configurable;

use App\Contracts\OrderableInterface;
use App\Contracts\PromoCodeRepositoryInterface;
use App\Contracts\RuleSpecificationInterface;
use App\Exceptions\RuleValidationException;

class GlobalUsageLimitRule implements RuleSpecificationInterface
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
        $currentUses = $this->repository->getGlobalUsageCount($this->promoCode, $context->currentOrders);

        if ($currentUses >= $this->maxUses) {
            throw new RuleValidationException('usage_limit_reached', 'This promo code has reached its maximum global usage limit.');
        }

        return true;
    }
}
