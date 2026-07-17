<?php

namespace Tests\Unit\Rules\Configurable;

use App\Rules\Configurable\GlobalAmountLimitRule;
use PHPUnit\Framework\TestCase;
use Tests\Factories\OrderContextFactory;
use Tests\Fakes\FakeOrder;
use Tests\Fakes\FakePromoCodeRepository;

class GlobalAmountLimitRuleTest extends TestCase
{
    public function test_it_allows_order_when_global_amount_is_below_limit()
    {
        $repository = new FakePromoCodeRepository();
        $repository->globalDiscountAmount = 950.0;
        
        $rule = new GlobalAmountLimitRule('PROMO20', 1000.0, $repository);
        $context = OrderContextFactory::new()->create();
        $order = new FakeOrder(100.0, $context);
        
        $result = $rule->isSatisfiedBy($order);
        
        $this->assertTrue($result->isValid);
        $this->assertNull($result->errorCode);
    }

    public function test_it_blocks_order_and_throws_exception_when_global_amount_reaches_limit()
    {
        $repository = new FakePromoCodeRepository();
        $repository->globalDiscountAmount = 1000.0;

        $rule = new GlobalAmountLimitRule('PROMO20', 1000.0, $repository);
        $context = OrderContextFactory::new()->create();
        $order = new FakeOrder(100.0, $context);

        $result = $rule->isSatisfiedBy($order);
        
        $this->assertFalse($result->isValid);
        $this->assertEquals('maximum_discount_reached', $result->errorCode);
    }
}
