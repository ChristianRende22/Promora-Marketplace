<?php

namespace Tests\Unit\Rules\Configurable;

use App\Rules\Configurable\FirstOrderOnlyRule;
use PHPUnit\Framework\TestCase;
use Tests\Factories\OrderContextFactory;
use Tests\Fakes\FakeOrder;

class FirstOrderOnlyRuleTest extends TestCase
{
    public function test_it_allows_order_when_buyer_has_no_previous_orders()
    {
        $rule = new FirstOrderOnlyRule();
        $context = OrderContextFactory::new()->withIsFirstOrder(true)->create();
        $order = new FakeOrder(100.0, $context);
        
        $result = $rule->isSatisfiedBy($order);
        
        $this->assertTrue($result->isValid);
        $this->assertNull($result->errorCode);
    }

    public function test_it_blocks_order_and_throws_exception_when_buyer_has_previous_orders()
    {
        $rule = new FirstOrderOnlyRule();
        $context = OrderContextFactory::new()->withIsFirstOrder(false)->create();
        $order = new FakeOrder(100.0, $context);

        $result = $rule->isSatisfiedBy($order);
        
        $this->assertFalse($result->isValid);
        $this->assertEquals('code_already_used', $result->errorCode);
    }
}
