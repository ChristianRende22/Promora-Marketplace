<?php

namespace Tests\Unit\Rules\Configurable;

use App\Rules\Configurable\MinPurchaseAmountRule;
use PHPUnit\Framework\TestCase;
use Tests\Factories\OrderContextFactory;
use Tests\Fakes\FakeOrder;

class MinPurchaseAmountRuleTest extends TestCase
{
    public function test_it_allows_order_when_subtotal_is_greater_than_or_equal_to_minimum()
    {
        $rule = new MinPurchaseAmountRule(100.0);
        $context = OrderContextFactory::new()->create();
        
        $orderExact = new FakeOrder(100.0, $context);
        $this->assertTrue($rule->isSatisfiedBy($orderExact));
        
        $orderGreater = new FakeOrder(150.0, $context);
        $this->assertTrue($rule->isSatisfiedBy($orderGreater));
    }

    public function test_it_blocks_order_and_throws_exception_when_subtotal_is_less_than_minimum()
    {
        $rule = new MinPurchaseAmountRule(100.0);
        $context = OrderContextFactory::new()->create();
        $order = new FakeOrder(99.99, $context);

        $result = $rule->isSatisfiedBy($order);
        
        $this->assertFalse($result->isValid);
        $this->assertEquals('min_amount_required', $result->errorCode);
    }
}
