<?php

namespace Tests\Unit\Domain\PromoCode\Rules;

use App\Domain\PromoCode\Exceptions\RuleValidationException;
use App\Domain\PromoCode\Rules\GlobalUsageLimitRule;
use PHPUnit\Framework\TestCase;
use Tests\Domain\PromoCode\Factories\OrderContextFactory;
use Tests\Domain\PromoCode\Fakes\FakeOrder;
use Tests\Domain\PromoCode\Fakes\FakePromoCodeRepository;

class GlobalUsageLimitRuleTest extends TestCase
{
    public function test_it_allows_order_when_global_usage_is_below_limit()
    {
        $repository = new FakePromoCodeRepository();
        $repository->globalUsageCount = 99;
        
        $rule = new GlobalUsageLimitRule('PROMO20', 100, $repository);
        $context = OrderContextFactory::new()->create();
        $order = new FakeOrder(100.0, $context);
        
        $this->assertTrue($rule->isSatisfiedBy($order));
    }

    public function test_it_blocks_order_and_throws_exception_when_global_usage_reaches_limit()
    {
        $repository = new FakePromoCodeRepository();
        $repository->globalUsageCount = 100;

        $rule = new GlobalUsageLimitRule('PROMO20', 100, $repository);
        $context = OrderContextFactory::new()->create();
        $order = new FakeOrder(100.0, $context);

        try {
            $rule->isSatisfiedBy($order);
            $this->fail('Expected RuleValidationException was not thrown');
        } catch (RuleValidationException $e) {
            $this->assertEquals('usage_limit_reached', $e->getErrorCode());
        }
    }
}
