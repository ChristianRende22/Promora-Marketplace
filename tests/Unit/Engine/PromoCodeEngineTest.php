<?php

declare(strict_types=1);

namespace Tests\Unit\Engine;

use App\Contracts\OrderableInterface;
use App\Contracts\RuleSpecificationInterface;
use App\Engine\PromoCodeEngine;
use App\ValueObjects\ValidationResult;
use PHPUnit\Framework\TestCase;
use Tests\Factories\OrderContextFactory;
use Tests\Fakes\FakeOrder;

/**
 * Según el ASD: PromoCodeEngine evalúa únicamente la colección de
 * RuleSpecificationInterface (reglas configurables) que PromoCodeRuleFactory
 * ya construyó. Las 3 reglas fijas las corre el caso de uso directamente
 * contra FixedRuleChain, antes de llegar aquí.
 */
class PromoCodeEngineTest extends TestCase
{
    public function test_it_returns_success_when_all_configurable_rules_are_satisfied(): void
    {
        $engine = new PromoCodeEngine([
            $this->passingConfigurableRule(),
            $this->passingConfigurableRule(),
        ]);

        $result = $engine->validate($this->fakeOrder());

        $this->assertTrue($result->isValid);
    }

    public function test_it_stops_at_the_first_configurable_rule_that_fails_and_does_not_evaluate_the_rest(): void
    {
        $secondRuleWasCalled = false;
        $secondRule = $this->passingConfigurableRule(function () use (&$secondRuleWasCalled): void {
            $secondRuleWasCalled = true;
        });

        $engine = new PromoCodeEngine([
            $this->failingConfigurableRule('min_amount_required'),
            $secondRule,
        ]);

        $result = $engine->validate($this->fakeOrder());

        $this->assertFalse($result->isValid);
        $this->assertEquals('min_amount_required', $result->errorCode);
        $this->assertFalse($secondRuleWasCalled);
    }

    private function fakeOrder(): OrderableInterface
    {
        $context = OrderContextFactory::new()
            ->withBuyerProfile('buyer-1')
            ->withCategoryId('cat-1')
            ->create();

        return new FakeOrder(100.0, $context);
    }

    private function passingConfigurableRule(?callable $onCall = null): RuleSpecificationInterface
    {
        return new class ($onCall) implements RuleSpecificationInterface {
            public function __construct(private readonly mixed $onCall)
            {
            }

            public function isSatisfiedBy(OrderableInterface $order): ValidationResult
            {
                if ($this->onCall !== null) {
                    ($this->onCall)();
                }

                return ValidationResult::success();
            }
        };
    }

    private function failingConfigurableRule(string $errorCode): RuleSpecificationInterface
    {
        return new class ($errorCode) implements RuleSpecificationInterface {
            public function __construct(private readonly string $errorCode)
            {
            }

            public function isSatisfiedBy(OrderableInterface $order): ValidationResult
            {
                return ValidationResult::failed($this->errorCode);
            }
        };
    }
}
