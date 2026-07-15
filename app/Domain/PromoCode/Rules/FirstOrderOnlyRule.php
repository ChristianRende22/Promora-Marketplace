<?php

namespace App\Domain\PromoCode\Rules;

use App\Domain\PromoCode\Contracts\OrderableInterface;
use App\Domain\PromoCode\Contracts\PromoCodeRepositoryInterface;
use App\Domain\PromoCode\Contracts\RuleSpecificationInterface;
use App\Domain\PromoCode\Exceptions\RuleValidationException;

class FirstOrderOnlyRule implements RuleSpecificationInterface
{
    public function __construct(private readonly PromoCodeRepositoryInterface $repository)
    {
    }

    public function isSatisfiedBy(OrderableInterface $order): bool
    {
        $context = $order->getOrderContext();
        
        if ($this->repository->hasPreviousOrders($context->buyerProfile, $context->currentOrders)) {
            throw new RuleValidationException('code_already_used', 'This promo code is valid for first-time orders only.');
        }

        return true;
    }
}
