<?php

namespace Tests\Factories;

use App\ValueObjects\OrderContext;
use App\ValueObjects\BuyerProfile;
use App\ValueObjects\OrderCollection;
use Tests\Fakes\FakeCategory;

class OrderContextFactory
{
    private bool $isFirstOrder = false;

    public static function new(): self
    {
        return new self();
    }

    public function withBuyerProfile(string $profile): self
    {
        $this->buyerProfile = $profile;
        return $this;
    }
    
    public function withIsFirstOrder(bool $isFirstOrder): self
    {
        $this->isFirstOrder = $isFirstOrder;
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
        $buyer = new BuyerProfile($this->buyerProfile, $this->isFirstOrder);
        $category = new FakeCategory($this->categoryId);
        $orders = new OrderCollection();
        
        return new OrderContext(
            $buyer,
            $category,
            $orders
        );
    }
}
