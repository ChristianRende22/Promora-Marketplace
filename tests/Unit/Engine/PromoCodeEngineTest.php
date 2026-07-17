<?php

declare(strict_types=1);

namespace Tests\Unit\Engine;

use App\Contracts\OrderableInterface;
use App\Contracts\RuleSpecificationInterface;
use App\Engine\PromoCodeEngine;
use App\Exceptions\RuleValidationException;
use PHPUnit\Framework\TestCase;
use Tests\Factories\OrderContextFactory;
use Tests\Fakes\FakeOrder;

/**
 * Según el ASD (sección "Colaboración entre patrones"): PromoCodeEngine evalúa
 * únicamente la colección de RuleSpecificationInterface (reglas configurables)
 * que PromoCodeRuleFactory ya construyó. Las 3 reglas fijas las corre el
 * caso de uso directamente contra FixedRuleChain, antes de llegar aquí.
 */
class PromoCodeEngineTest extends TestCase
{
    public function test_it_passes_validation_when_all_configurable_rules_are_satisfied(): void
    {
        $engine = new PromoCodeEngine([
            $this->passingConfigurableRule(),
            $this->passingConfigurableRule(),
        ]);

        $engine->validate($this->fakeOrder());

        $this->addToAssertionCount(1);
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

        try {
            $engine->validate($this->fakeOrder());
            $this->fail('Expected RuleValidationException was not thrown');
        } catch (RuleValidationException $e) {
            $this->assertEquals('min_amount_required', $e->getErrorCode());
        }

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
