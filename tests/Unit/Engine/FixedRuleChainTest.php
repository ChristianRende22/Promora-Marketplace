<?php

declare(strict_types=1);

namespace Tests\Unit\Engine;

use App\Contracts\FixedValidationRuleInterface;
use App\Engine\FixedRuleChain;
use App\Entities\PromoCode;
use App\Exceptions\RuleValidationException;
use PHPUnit\Framework\TestCase;
use Tests\Factories\PromoCodeFactory;

class FixedRuleChainTest extends TestCase
{
    public function test_it_runs_all_rules_in_order_when_all_pass(): void
    {
        $calls = [];
        $ruleA = $this->passingRule(function () use (&$calls): void {
            $calls[] = 'A';
        });
        $ruleB = $this->passingRule(function () use (&$calls): void {
            $calls[] = 'B';
        });

        $chain = new FixedRuleChain([$ruleA, $ruleB]);
        $promoCode = PromoCodeFactory::new()->create();

        $chain->run($promoCode);

        $this->assertEquals(['A', 'B'], $calls);
    }

    public function test_it_stops_at_the_first_rule_that_fails_and_does_not_run_the_rest(): void
    {
        $calls = [];
        $ruleA = $this->failingRule('invalid_code', function () use (&$calls): void {
            $calls[] = 'A';
        });
        $ruleB = $this->passingRule(function () use (&$calls): void {
            $calls[] = 'B';
        });

        $chain = new FixedRuleChain([$ruleA, $ruleB]);
        $promoCode = PromoCodeFactory::new()->create();

        try {
            $chain->run($promoCode);
            $this->fail('Expected RuleValidationException was not thrown');
        } catch (RuleValidationException $e) {
            $this->assertEquals('invalid_code', $e->getErrorCode());
        }

        $this->assertEquals(['A'], $calls);
    }

    private function passingRule(callable $onCall): FixedValidationRuleInterface
    {
        return new class ($onCall) implements FixedValidationRuleInterface {
            public function __construct(private readonly mixed $onCall)
            {
            }

            public function isSatisfiedBy(?PromoCode $promoCode): bool
            {
                ($this->onCall)();

                return true;
            }
        };
    }

    private function failingRule(string $errorCode, callable $onCall): FixedValidationRuleInterface
    {
        return new class ($errorCode, $onCall) implements FixedValidationRuleInterface {
            public function __construct(
                private readonly string $errorCode,
                private readonly mixed $onCall
            ) {
            }

            public function isSatisfiedBy(?PromoCode $promoCode): bool
            {
                ($this->onCall)();

                throw new RuleValidationException($this->errorCode, 'blocked');
            }
        };
    }
}
