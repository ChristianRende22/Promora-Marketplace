<?php

namespace Tests\Unit\Rules\Configurable;

use App\Exceptions\RuleValidationException;
use App\Rules\Configurable\RestrictedUsageRule;
use PHPUnit\Framework\TestCase;
use Tests\Factories\OrderContextFactory;
use Tests\Fakes\FakeOrder;
use Tests\Fakes\FakePromoCodeRepository;

class RestrictedUsageRuleTest extends TestCase
{
    public function test_it_allows_order_when_user_is_in_restricted_list()
    {
        $repository = new FakePromoCodeRepository();
        $repository->isUserRestricted = true;
        
        $rule = new RestrictedUsageRule('VIP_ONLY', $repository);
        $context = OrderContextFactory::new()->create();
        $order = new FakeOrder(100.0, $context);
        
        $this->assertTrue($rule->isSatisfiedBy($order));
    }

    public function test_it_blocks_order_and_throws_exception_when_user_is_not_in_restricted_list()
    {
        $repository = new FakePromoCodeRepository();
        $repository->isUserRestricted = false;

        $rule = new RestrictedUsageRule('VIP_ONLY', $repository);
        $context = OrderContextFactory::new()->create();
        $order = new FakeOrder(100.0, $context);

        try {
            $rule->isSatisfiedBy($order);
            $this->fail('Expected RuleValidationException was not thrown');
        } catch (RuleValidationException $e) {
            $this->assertEquals('restricted_usage', $e->getErrorCode());
        }
    }
}
