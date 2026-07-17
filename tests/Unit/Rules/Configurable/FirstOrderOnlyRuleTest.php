<?php

namespace Tests\Unit\Rules\Configurable;

use App\Exceptions\RuleValidationException;
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
        
        $this->assertTrue($rule->isSatisfiedBy($order));
    }

    public function test_it_blocks_order_and_throws_exception_when_buyer_has_previous_orders()
    {
        $rule = new FirstOrderOnlyRule();
        $context = OrderContextFactory::new()->withIsFirstOrder(false)->create();
        $order = new FakeOrder(100.0, $context);

        try {
            $rule->isSatisfiedBy($order);
            $this->fail('Expected RuleValidationException was not thrown');
        } catch (RuleValidationException $e) {
            $this->assertEquals('code_already_used', $e->getErrorCode());
        }
    }
}
