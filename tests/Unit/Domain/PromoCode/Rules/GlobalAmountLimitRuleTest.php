<?php

namespace Tests\Unit\Domain\PromoCode\Rules;

use App\Domain\PromoCode\Exceptions\RuleValidationException;
use App\Domain\PromoCode\Rules\GlobalAmountLimitRule;
use PHPUnit\Framework\TestCase;
use Tests\Domain\PromoCode\Factories\OrderContextFactory;
use Tests\Domain\PromoCode\Fakes\FakeOrder;
use Tests\Domain\PromoCode\Fakes\FakePromoCodeRepository;

class GlobalAmountLimitRuleTest extends TestCase
{
    public function test_it_allows_order_when_global_amount_is_below_limit()
    {
        $repository = new FakePromoCodeRepository();
        $repository->globalDiscountAmount = 950.0;
        
        $rule = new GlobalAmountLimitRule('PROMO20', 1000.0, $repository);
        $context = OrderContextFactory::new()->create();
        $order = new FakeOrder(100.0, $context);
        
        $this->assertTrue($rule->isSatisfiedBy($order));
    }

    public function test_it_blocks_order_and_throws_exception_when_global_amount_reaches_limit()
    {
        $repository = new FakePromoCodeRepository();
        $repository->globalDiscountAmount = 1000.0;

        $rule = new GlobalAmountLimitRule('PROMO20', 1000.0, $repository);
        $context = OrderContextFactory::new()->create();
        $order = new FakeOrder(100.0, $context);

        try {
            $rule->isSatisfiedBy($order);
            $this->fail('Expected RuleValidationException was not thrown');
        } catch (RuleValidationException $e) {
            $this->assertEquals('maximum_discount_reached', $e->getErrorCode());
        }
    }
}
