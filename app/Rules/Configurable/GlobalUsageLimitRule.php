<?php

namespace App\Rules\Configurable;

use App\Contracts\OrderableInterface;
use App\Contracts\PromoCodeRepositoryInterface;
use App\Contracts\RuleSpecificationInterface;
use App\ValueObjects\ValidationResult;

class GlobalUsageLimitRule implements RuleSpecificationInterface
{
    public function __construct(
        private readonly string $promoCode,
        private readonly int $maxUses,
        private readonly PromoCodeRepositoryInterface $repository
    ) {
    }

    public function isSatisfiedBy(OrderableInterface $order): ValidationResult
    {
        $context = $order->getOrderContext();
        $currentUses = $this->repository->getGlobalUsageCount($this->promoCode, $context->currentOrders);

        if ($currentUses >= $this->maxUses) {
            return ValidationResult::failed('usage_limit_reached');
        }

        return ValidationResult::success();
    }
}
