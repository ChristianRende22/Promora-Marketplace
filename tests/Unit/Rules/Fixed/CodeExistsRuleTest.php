<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Fixed;

use App\Rules\Fixed\CodeExistsRule;
use PHPUnit\Framework\TestCase;
use Tests\Factories\PromoCodeFactory;

class CodeExistsRuleTest extends TestCase
{
    public function test_it_allows_when_promo_code_exists(): void
    {
        $rule = new CodeExistsRule();
        $promoCode = PromoCodeFactory::new()->create();

        $result = $rule->isSatisfiedBy($promoCode);

        $this->assertTrue($result->isValid);
    }

    public function test_it_blocks_when_promo_code_is_null(): void
    {
        $rule = new CodeExistsRule();

        $result = $rule->isSatisfiedBy(null);

        $this->assertFalse($result->isValid);
        $this->assertEquals('invalid_code', $result->errorCode);
    }
}
