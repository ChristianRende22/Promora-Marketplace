<?php

declare(strict_types=1);

namespace Tests\Unit\Application\Factories;

use App\Application\Factories\PromoCodeRuleFactory;
use App\Contracts\RuleSpecificationInterface;
use App\Rules\Configurable\FirstOrderOnlyRule;
use App\Rules\Configurable\MinPurchaseAmountRule;
use App\Rules\Configurable\UserUsageLimitRule;
use PHPUnit\Framework\TestCase;
use Tests\Fakes\FakePromoCodeRepository;

class PromoCodeRuleFactoryTest extends TestCase
{
    public function test_it_builds_the_requested_rules_based_on_configuration()
    {
        $repository = new FakePromoCodeRepository();
        $factory = new PromoCodeRuleFactory($repository);

        $configuration = [
            'min_purchase_amount' => 50.0,
            'first_order_only' => true,
            'user_usage_limit' => 3,
        ];

        $rules = $factory->buildRules('PROMO_TEST', $configuration);

        $this->assertCount(3, $rules);
        
        $this->assertInstanceOf(RuleSpecificationInterface::class, $rules[0]);
        $this->assertInstanceOf(MinPurchaseAmountRule::class, $rules[0]);

        $this->assertInstanceOf(RuleSpecificationInterface::class, $rules[1]);
        $this->assertInstanceOf(FirstOrderOnlyRule::class, $rules[1]);

        $this->assertInstanceOf(RuleSpecificationInterface::class, $rules[2]);
        $this->assertInstanceOf(UserUsageLimitRule::class, $rules[2]);
    }
    
    public function test_it_returns_empty_array_if_no_rules_configured()
    {
        $repository = new FakePromoCodeRepository();
        $factory = new PromoCodeRuleFactory($repository);

        $rules = $factory->buildRules('PROMO_TEST', []);

        $this->assertIsArray($rules);
        $this->assertEmpty($rules);
    }
}
