<?php

namespace Tests\Unit\Domain\PromoCode\Rules;

use App\Domain\PromoCode\Exceptions\RuleValidationException;
use App\Domain\PromoCode\Rules\UserUsageLimitRule;
use PHPUnit\Framework\TestCase;
use Tests\Domain\PromoCode\Factories\OrderContextFactory;
use Tests\Domain\PromoCode\Fakes\FakeOrder;
use Tests\Domain\PromoCode\Fakes\FakePromoCodeRepository;

class UserUsageLimitRuleTest extends TestCase
{
    public function test_it_allows_order_when_user_usage_is_below_limit()
    {
        $repository = new FakePromoCodeRepository();
        $repository->userUsageCount = 1;
        
        $rule = new UserUsageLimitRule('PROMO20', 2, $repository);
        $context = OrderContextFactory::new()->create();
        $order = new FakeOrder(100.0, $context);
        
        $this->assertTrue($rule->isSatisfiedBy($order));
    }

    public function test_it_blocks_order_and_throws_exception_when_user_usage_reaches_limit()
    {
        $repository = new FakePromoCodeRepository();
        $repository->userUsageCount = 2;

        $rule = new UserUsageLimitRule('PROMO20', 2, $repository);
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
