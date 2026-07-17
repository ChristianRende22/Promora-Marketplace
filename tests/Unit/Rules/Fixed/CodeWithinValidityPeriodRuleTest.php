<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Fixed;

use App\Exceptions\RuleValidationException;
use App\Rules\Fixed\CodeWithinValidityPeriodRule;
use DateTimeImmutable;
use PHPUnit\Framework\TestCase;
use Tests\Factories\PromoCodeFactory;

class CodeWithinValidityPeriodRuleTest extends TestCase
{
    public function test_it_allows_when_current_date_is_within_validity_period(): void
    {
        $rule = new CodeWithinValidityPeriodRule();
        $promoCode = PromoCodeFactory::new()
            ->withValidityPeriod(new DateTimeImmutable('-1 day'), new DateTimeImmutable('+1 day'))
            ->create();

        $this->assertTrue($rule->isSatisfiedBy($promoCode));
    }

    public function test_it_blocks_and_throws_exception_when_code_has_not_started_yet(): void
    {
        $rule = new CodeWithinValidityPeriodRule();
        $promoCode = PromoCodeFactory::new()
            ->withValidityPeriod(new DateTimeImmutable('+1 day'), new DateTimeImmutable('+2 days'))
            ->create();

        try {
            $rule->isSatisfiedBy($promoCode);
            $this->fail('Expected RuleValidationException was not thrown');
        } catch (RuleValidationException $e) {
            $this->assertEquals('expired_coupon', $e->getErrorCode());
        }
    }

    public function test_it_blocks_and_throws_exception_when_code_has_already_expired(): void
    {
        $rule = new CodeWithinValidityPeriodRule();
        $promoCode = PromoCodeFactory::new()
            ->withValidityPeriod(new DateTimeImmutable('-2 days'), new DateTimeImmutable('-1 day'))
            ->create();

        try {
            $rule->isSatisfiedBy($promoCode);
            $this->fail('Expected RuleValidationException was not thrown');
        } catch (RuleValidationException $e) {
            $this->assertEquals('expired_coupon', $e->getErrorCode());
        }
    }
}
