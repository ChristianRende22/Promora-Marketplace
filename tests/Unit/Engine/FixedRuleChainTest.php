<?php

declare(strict_types=1);

namespace Tests\Unit\Engine;

use App\Contracts\FixedValidationRuleInterface;
use App\Engine\FixedRuleChain;
use App\Entities\PromoCode;
use App\ValueObjects\ValidationResult;
use PHPUnit\Framework\TestCase;
use Tests\Factories\PromoCodeFactory;

class FixedRuleChainTest extends TestCase
{
    public function test_it_returns_success_when_all_rules_pass(): void
    {
        $chain = new FixedRuleChain([
            $this->passingRule(),
            $this->passingRule(),
        ]);

        $result = $chain->run(PromoCodeFactory::new()->create());

        $this->assertTrue($result->isValid);
    }

    public function test_it_stops_at_the_first_rule_that_fails_and_does_not_run_the_rest(): void
    {
        $secondRuleWasCalled = false;
        $secondRule = $this->passingRule(function () use (&$secondRuleWasCalled): void {
            $secondRuleWasCalled = true;
        });

        $chain = new FixedRuleChain([
            $this->failingRule('invalid_code'),
            $secondRule,
        ]);

        $result = $chain->run(PromoCodeFactory::new()->create());

        $this->assertFalse($result->isValid);
        $this->assertEquals('invalid_code', $result->errorCode);
        $this->assertFalse($secondRuleWasCalled);
    }

    private function passingRule(?callable $onCall = null): FixedValidationRuleInterface
    {
        return new class ($onCall) implements FixedValidationRuleInterface {
            public function __construct(private readonly mixed $onCall)
            {
            }

            public function isSatisfiedBy(?PromoCode $promoCode): ValidationResult
            {
                if ($this->onCall !== null) {
                    ($this->onCall)();
                }

                return ValidationResult::success();
            }
        };
    }

    private function failingRule(string $errorCode): FixedValidationRuleInterface
    {
        return new class ($errorCode) implements FixedValidationRuleInterface {
            public function __construct(private readonly string $errorCode)
            {
            }

            public function isSatisfiedBy(?PromoCode $promoCode): ValidationResult
            {
                return ValidationResult::failed($this->errorCode);
            }
        };
    }
}
