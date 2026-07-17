<?php

declare(strict_types=1);

namespace Tests\Unit\Rules\Fixed;

use App\Enums\PromoCodeStatus;
use App\Exceptions\RuleValidationException;
use App\Rules\Fixed\CodeIsActiveRule;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;
use Tests\Factories\PromoCodeFactory;

class CodeIsActiveRuleTest extends TestCase
{
    public function test_it_allows_when_status_is_active(): void
    {
        $rule = new CodeIsActiveRule();
        $promoCode = PromoCodeFactory::new()->withStatus(PromoCodeStatus::Active)->create();

        $this->assertTrue($rule->isSatisfiedBy($promoCode));
    }

    #[DataProvider('nonActiveStatusProvider')]
    public function test_it_blocks_and_throws_exception_when_status_is_not_active(PromoCodeStatus $status): void
    {
        $rule = new CodeIsActiveRule();
        $promoCode = PromoCodeFactory::new()->withStatus($status)->create();

        try {
            $rule->isSatisfiedBy($promoCode);
            $this->fail('Expected RuleValidationException was not thrown');
        } catch (RuleValidationException $e) {
            $this->assertEquals('invalid_code', $e->getErrorCode());
        }
    }

    public static function nonActiveStatusProvider(): array
    {
        return [
            'draft' => [PromoCodeStatus::Draft],
            'paused' => [PromoCodeStatus::Paused],
            'expired' => [PromoCodeStatus::Expired],
        ];
    }
}
