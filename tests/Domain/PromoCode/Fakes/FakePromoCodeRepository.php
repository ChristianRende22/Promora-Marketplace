<?php

namespace Tests\Domain\PromoCode\Fakes;

use App\Domain\PromoCode\Contracts\PromoCodeRepositoryInterface;

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

    public function getUserUsageCount(string $promoCode, string $buyerProfile, array $excludedOrderIds = []): int
    {
        return $this->userUsageCount;
    }

    public function getGlobalUsageCount(string $promoCode, array $excludedOrderIds = []): int
    {
        return $this->globalUsageCount;
    }

    public function getGlobalDiscountAmount(string $promoCode, array $excludedOrderIds = []): float
    {
        return $this->globalDiscountAmount;
    }

    public function isUserInRestrictedList(string $promoCode, string $buyerProfile): bool
    {
        return $this->isUserRestricted;
    }
}
