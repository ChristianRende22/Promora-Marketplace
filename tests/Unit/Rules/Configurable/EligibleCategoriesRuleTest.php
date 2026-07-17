<?php

namespace Tests\Unit\Rules\Configurable;

use App\Exceptions\RuleValidationException;
use App\Rules\Configurable\EligibleCategoriesRule;
use PHPUnit\Framework\TestCase;
use Tests\Factories\OrderContextFactory;
use Tests\Fakes\FakeOrder;

class EligibleCategoriesRuleTest extends TestCase
{
    public function test_it_allows_order_when_category_is_eligible()
    {
        $rule = new EligibleCategoriesRule([1, 2, 3]); 
        $context = OrderContextFactory::new()->withCategoryId(3)->create();
        $order = new FakeOrder(100.0, $context);
        
        $this->assertTrue($rule->isSatisfiedBy($order));
    }

    public function test_it_blocks_order_and_throws_exception_when_category_is_not_eligible()
    {
        $rule = new EligibleCategoriesRule([1, 2]);
        $context = OrderContextFactory::new()->withCategoryId(99)->create();
        $order = new FakeOrder(100.0, $context);

        try {
            $rule->isSatisfiedBy($order);
            $this->fail('Expected RuleValidationException was not thrown');
        } catch (RuleValidationException $e) {
            $this->assertEquals('invalid_code', $e->getErrorCode());
        }
    }
}
