<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Fixed;

use App\Exceptions\RuleValidationException;
use App\Rules\Fixed\CodeExistsRule;
use PHPUnit\Framework\TestCase;
use Tests\Factories\PromoCodeFactory;

class CodeExistsRuleTest extends TestCase
{
    public function test_it_allows_when_promo_code_exists(): void
    {
        $rule = new CodeExistsRule();
        $promoCode = PromoCodeFactory::new()->create();

        $this->assertTrue($rule->isSatisfiedBy($promoCode));
    }

    public function test_it_blocks_and_throws_exception_when_promo_code_is_null(): void
    {
        $rule = new CodeExistsRule();

        try {
            $rule->isSatisfiedBy(null);
            $this->fail('Expected RuleValidationException was not thrown');
        } catch (RuleValidationException $e) {
            $this->assertEquals('invalid_code', $e->getErrorCode());
        }
    }
}
