<?php

namespace Tests\Domain\PromoCode\Factories;

use App\Domain\PromoCode\ValueObjects\OrderContext;

class OrderContextFactory
{
    private string $buyerProfile = 'default_buyer';
    private int|string $categoryId = 1;
    private array $currentOrders = [];

    public static function new(): self
    {
        return new self();
    }

    public function withBuyerProfile(string $profile): self
    {
        $this->buyerProfile = $profile;
        return $this;
    }

    public function withCategoryId(int|string $categoryId): self
    {
        $this->categoryId = $categoryId;
        return $this;
    }

    public function withCurrentOrders(array $orders): self
    {
        $this->currentOrders = $orders;
        return $this;
    }

    public function create(): OrderContext
    {
        return new OrderContext(
            $this->buyerProfile,
            $this->categoryId,
            $this->currentOrders
        );
    }
}
