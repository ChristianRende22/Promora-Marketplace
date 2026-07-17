<?php

namespace Tests\Fakes;

use App\Contracts\PromoCodeRepositoryInterface;
use App\Contracts\OrderCollectionInterface;

class FakePromoCodeRepository implements PromoCodeRepositoryInterface
{
    public bool $hasPreviousOrders = false;
    public int $userUsageCount = 0;
    public int $globalUsageCount = 0;
    public float $globalDiscountAmount = 0.0;
    public bool $isUserRestricted = false; 
    
    public function hasPreviousOrders(string $buyerProfile, array $excludedOrderIds = []): bool
    {
        return $this->hasPreviousOrders;
    }

    public function getUserUsageCount(string $promoCode, string|int $buyerProfileId, OrderCollectionInterface $excludedOrders): int
    {
        return $this->userUsageCount;
    }

    public function getGlobalUsageCount(string $promoCode, OrderCollectionInterface $excludedOrders): int
    {
        return $this->globalUsageCount;
    }

    public function getGlobalDiscountAmount(string $promoCode, OrderCollectionInterface $excludedOrders): float
    {
        return $this->globalDiscountAmount;
    }

    public function isUserInRestrictedList(string $promoCode, string|int $buyerProfileId): bool
    {
        return $this->isUserRestricted;
    }
}
