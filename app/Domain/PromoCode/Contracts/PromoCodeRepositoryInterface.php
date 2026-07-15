<?php

namespace App\Domain\PromoCode\Contracts;

interface PromoCodeRepositoryInterface
{
    public function hasPreviousOrders(string $buyerProfile, array $excludedOrderIds = []): bool;
    
    public function getUserUsageCount(string $promoCode, string $buyerProfile, array $excludedOrderIds = []): int;
    
    public function getGlobalUsageCount(string $promoCode, array $excludedOrderIds = []): int;
    
    public function getGlobalDiscountAmount(string $promoCode, array $excludedOrderIds = []): float;
    
    public function isUserInRestrictedList(string $promoCode, string $buyerProfile): bool;
}
