<?php

namespace App\Contracts;

interface PromoCodeRepositoryInterface
{
    public function hasPreviousOrders(string $buyerProfile, array $excludedOrderIds = []): bool;
    
    public function getUserUsageCount(string $promoCode, string|int $buyerProfileId, OrderCollectionInterface $excludedOrders): int;
    
    public function getGlobalUsageCount(string $promoCode, OrderCollectionInterface $excludedOrders): int;
    
    public function getGlobalDiscountAmount(string $promoCode, OrderCollectionInterface $excludedOrders): float;
    
    public function isUserInRestrictedList(string $promoCode, string|int $buyerProfileId): bool;
}
