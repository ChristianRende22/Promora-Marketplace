<?php

namespace Tests\Unit\Rules\Configurable;

use App\Rules\Configurable\GlobalUsageLimitRule;
use PHPUnit\Framework\TestCase;
use Tests\Factories\OrderContextFactory;
use Tests\Fakes\FakeOrder;
use Tests\Fakes\FakePromoCodeRepository;

class GlobalUsageLimitRuleTest extends TestCase
{
    public function test_it_allows_order_when_global_usage_is_below_limit()
    {
        $repository = new FakePromoCodeRepository();
        $repository->globalUsageCount = 99;
        
        $rule = new GlobalUsageLimitRule('PROMO20', 100, $repository);
        $context = OrderContextFactory::new()->create();
        $order = new FakeOrder(100.0, $context);
        
        $result = $rule->isSatisfiedBy($order);
        
        $this->assertTrue($result->isValid);
        $this->assertNull($result->errorCode);
    }

    public function test_it_blocks_order_and_throws_exception_when_global_usage_reaches_limit()
    {
        $repository = new FakePromoCodeRepository();
        $repository->globalUsageCount = 100;

        $rule = new GlobalUsageLimitRule('PROMO20', 100, $repository);
        $context = OrderContextFactory::new()->create();
        $order = new FakeOrder(100.0, $context);

        $result = $rule->isSatisfiedBy($order);
        
        $this->assertFalse($result->isValid);
        $this->assertEquals('usage_limit_reached', $result->errorCode);
    }
}
