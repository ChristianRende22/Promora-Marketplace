<?php

namespace Tests\Unit\Domain\PromoCode\Rules;

use App\Domain\PromoCode\Exceptions\RuleValidationException;
use App\Domain\PromoCode\Rules\FirstOrderOnlyRule;
use PHPUnit\Framework\TestCase;
use Tests\Domain\PromoCode\Factories\OrderContextFactory;
use Tests\Domain\PromoCode\Fakes\FakeOrder;
use Tests\Domain\PromoCode\Fakes\FakePromoCodeRepository;

class FirstOrderOnlyRuleTest extends TestCase
{
    public function test_it_allows_order_when_buyer_has_no_previous_orders()
    {
        $repository = new FakePromoCodeRepository();
        $repository->hasPreviousOrders = false;
        
        $rule = new FirstOrderOnlyRule($repository);
        $context = OrderContextFactory::new()->create();
        $order = new FakeOrder(100.0, $context);
        
        $this->assertTrue($rule->isSatisfiedBy($order));
    }

    public function test_it_blocks_order_and_throws_exception_when_buyer_has_previous_orders()
    {
        $repository = new FakePromoCodeRepository();
        $repository->hasPreviousOrders = true;

        $rule = new FirstOrderOnlyRule($repository);
        $context = OrderContextFactory::new()->create();
        $order = new FakeOrder(100.0, $context);

        try {
            $rule->isSatisfiedBy($order);
            $this->fail('Expected RuleValidationException was not thrown');
        } catch (RuleValidationException $e) {
            $this->assertEquals('code_already_used', $e->getErrorCode());
        }
    }
}
