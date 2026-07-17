<?php

declare(strict_types=1);

namespace Tests\Unit\Engine;

use App\Contracts\FixedValidationRuleInterface;
use App\Contracts\OrderableInterface;
use App\Contracts\RuleSpecificationInterface;
use App\Engine\FixedRuleChain;
use App\Engine\PromoCodeEngine;
use App\Entities\PromoCode;
use App\Exceptions\RuleValidationException;
use PHPUnit\Framework\TestCase;
use Tests\Factories\OrderContextFactory;
use Tests\Factories\PromoCodeFactory;
use Tests\Fakes\FakeOrder;

class PromoCodeEngineTest extends TestCase
{
    public function test_it_passes_validation_when_fixed_and_configurable_rules_are_satisfied(): void
    {
        $engine = new PromoCodeEngine(
            new FixedRuleChain([$this->passingFixedRule()]),
            [$this->passingConfigurableRule()]
        );

        $engine->validate(PromoCodeFactory::new()->create(), $this->fakeOrder());

        $this->addToAssertionCount(1);
    }

    public function test_it_stops_at_fixed_rules_and_never_evaluates_configurable_rules_when_a_fixed_rule_fails(): void
    {
        $configurableWasCalled = false;
        $configurableRule = $this->passingConfigurableRule(function () use (&$configurableWasCalled): void {
            $configurableWasCalled = true;
        });

        $engine = new PromoCodeEngine(
            new FixedRuleChain([$this->failingFixedRule('invalid_code')]),
            [$configurableRule]
        );

        try {
            $engine->validate(PromoCodeFactory::new()->create(), $this->fakeOrder());
            $this->fail('Expected RuleValidationException was not thrown');
        } catch (RuleValidationException $e) {
            $this->assertEquals('invalid_code', $e->getErrorCode());
        }

        $this->assertFalse($configurableWasCalled);
    }

    public function test_it_evaluates_configurable_rules_only_after_fixed_rules_pass_and_propagates_their_failure(): void
    {
        $engine = new PromoCodeEngine(
            new FixedRuleChain([$this->passingFixedRule()]),
            [$this->failingConfigurableRule('min_amount_required')]
        );

        try {
            $engine->validate(PromoCodeFactory::new()->create(), $this->fakeOrder());
            $this->fail('Expected RuleValidationException was not thrown');
        } catch (RuleValidationException $e) {
            $this->assertEquals('min_amount_required', $e->getErrorCode());
        }
    }

    private function fakeOrder(): OrderableInterface
    {
        $context = OrderContextFactory::new()
            ->withBuyerProfile('buyer-1')
            ->withCategoryId('cat-1')
            ->create();

        return new FakeOrder(100.0, $context);
    }

    private function passingFixedRule(): FixedValidationRuleInterface
    {
        return new class implements FixedValidationRuleInterface {
            public function isSatisfiedBy(?PromoCode $promoCode): bool
            {
                return true;
            }
        };
    }

    private function failingFixedRule(string $errorCode): FixedValidationRuleInterface
    {
        return new class ($errorCode) implements FixedValidationRuleInterface {
            public function __construct(private readonly string $errorCode)
            {
            }

            public function isSatisfiedBy(?PromoCode $promoCode): bool
            {
                throw new RuleValidationException($this->errorCode, 'blocked');
            }
        };
    }

    private function passingConfigurableRule(?callable $onCall = null): RuleSpecificationInterface
    {
        return new class ($onCall) implements RuleSpecificationInterface {
            public function __construct(private readonly mixed $onCall)
            {
            }

            public function isSatisfiedBy(OrderableInterface $order): bool
            {
                if ($this->onCall !== null) {
                    ($this->onCall)();
                }

                return true;
            }
        };
    }

    private function failingConfigurableRule(string $errorCode): RuleSpecificationInterface
    {
        return new class ($errorCode) implements RuleSpecificationInterface {
            public function __construct(private readonly string $errorCode)
            {
            }

            public function isSatisfiedBy(OrderableInterface $order): bool
            {
                throw new RuleValidationException($this->errorCode, 'blocked');
            }
        };
    }
}
