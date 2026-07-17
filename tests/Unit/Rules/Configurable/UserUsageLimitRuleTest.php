<?php

namespace Tests\Unit\Rules\Configurable;

use App\Rules\Configurable\UserUsageLimitRule;
use PHPUnit\Framework\TestCase;
use Tests\Factories\OrderContextFactory;
use Tests\Fakes\FakeOrder;
use Tests\Fakes\FakePromoCodeRepository;

class UserUsageLimitRuleTest extends TestCase
{
    public function test_it_allows_order_when_user_usage_is_below_limit()
    {
        $repository = new FakePromoCodeRepository();
        $repository->userUsageCount = 1;
        
        $rule = new UserUsageLimitRule('PROMO20', 2, $repository);
        $context = OrderContextFactory::new()->create();
        $order = new FakeOrder(100.0, $context);
        
        $result = $rule->isSatisfiedBy($order);
        
        $this->assertTrue($result->isValid);
        $this->assertNull($result->errorCode);
    }

    public function test_it_blocks_order_and_throws_exception_when_user_usage_reaches_limit()
    {
        $repository = new FakePromoCodeRepository();
        $repository->userUsageCount = 2;

        $rule = new UserUsageLimitRule('PROMO20', 2, $repository);
        $context = OrderContextFactory::new()->create();
        $order = new FakeOrder(100.0, $context);

        $result = $rule->isSatisfiedBy($order);
        
        $this->assertFalse($result->isValid);
        $this->assertEquals('usage_limit_reached', $result->errorCode);
    }
}
