<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Fixed;

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

        $result = $rule->isSatisfiedBy($promoCode);

        $this->assertTrue($result->isValid);
    }

    public function test_it_blocks_when_code_has_not_started_yet(): void
    {
        $rule = new CodeWithinValidityPeriodRule();
        $promoCode = PromoCodeFactory::new()
            ->withValidityPeriod(new DateTimeImmutable('+1 day'), new DateTimeImmutable('+2 days'))
            ->create();

        $result = $rule->isSatisfiedBy($promoCode);

        $this->assertFalse($result->isValid);
        $this->assertEquals('expired_coupon', $result->errorCode);
    }

    public function test_it_blocks_when_code_has_already_expired(): void
    {
        $rule = new CodeWithinValidityPeriodRule();
        $promoCode = PromoCodeFactory::new()
            ->withValidityPeriod(new DateTimeImmutable('-2 days'), new DateTimeImmutable('-1 day'))
            ->create();

        $result = $rule->isSatisfiedBy($promoCode);

        $this->assertFalse($result->isValid);
        $this->assertEquals('expired_coupon', $result->errorCode);
    }
}
