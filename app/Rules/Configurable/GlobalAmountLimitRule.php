<?php

namespace App\Rules\Configurable;

use App\Contracts\OrderableInterface;
use App\Contracts\PromoCodeRepositoryInterface;
use App\Contracts\RuleSpecificationInterface;
use App\Exceptions\RuleValidationException;

class GlobalAmountLimitRule implements RuleSpecificationInterface
{
    public function __construct(
        private readonly string $promoCode,
        private readonly float $maxAmount,
        private readonly PromoCodeRepositoryInterface $repository
    ) {
    }

    public function isSatisfiedBy(OrderableInterface $order): bool
    {
        $context = $order->getOrderContext();
        $currentAmount = $this->repository->getGlobalDiscountAmount($this->promoCode, $context->currentOrders);

        if ($currentAmount >= $this->maxAmount) {
            throw new RuleValidationException('maximum_discount_reached', 'This promo code has reached its maximum global discount amount.');
        }

        return true;
    }
}
