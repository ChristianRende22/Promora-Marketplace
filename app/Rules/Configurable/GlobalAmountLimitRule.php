<?php

namespace App\Rules\Configurable;

use App\Contracts\OrderableInterface;
use App\Contracts\PromoCodeRepositoryInterface;
use App\Contracts\RuleSpecificationInterface;
use App\ValueObjects\ValidationResult;

class GlobalAmountLimitRule implements RuleSpecificationInterface
{
    public function __construct(
        private readonly string $promoCode,
        private readonly float $maxAmount,
        private readonly PromoCodeRepositoryInterface $repository
    ) {
    }

    public function isSatisfiedBy(OrderableInterface $order): ValidationResult
    {
        $context = $order->getOrderContext();
        $currentAmount = $this->repository->getGlobalDiscountAmount($this->promoCode, $context->currentOrders);

        if ($currentAmount + $order->getSubtotal() > $this->maxAmount) {
            return ValidationResult::failed('maximum_discount_reached');
        }

        return ValidationResult::success();
    }
}
