<?php

declare(strict_types=1);

namespace App\Application\Factories;

use App\Contracts\PromoCodeRepositoryInterface;
use App\Rules\Configurable\EligibleCategoriesRule;
use App\Rules\Configurable\FirstOrderOnlyRule;
use App\Rules\Configurable\GlobalAmountLimitRule;
use App\Rules\Configurable\GlobalUsageLimitRule;
use App\Rules\Configurable\MinPurchaseAmountRule;
use App\Rules\Configurable\RestrictedUsageRule;
use App\Rules\Configurable\UserUsageLimitRule;

class PromoCodeRuleFactory
{
    public function __construct(
        private readonly PromoCodeRepositoryInterface $repository
    ) {
    }

    /**
     * Builds an array of RuleSpecificationInterface objects based on the provided configuration.
     * 
     * @param string $promoCode The promo code identifier
     * @param array $configuration The configuration array extracted from the database
     * @return \App\Contracts\RuleSpecificationInterface[]
     */
    public function buildRules(string $promoCode, array $configuration): array
    {
        $rules = [];

        if (isset($configuration['min_purchase_amount'])) {
            $rules[] = new MinPurchaseAmountRule((float) $configuration['min_purchase_amount']);
        }

        if (isset($configuration['eligible_categories']) && is_array($configuration['eligible_categories'])) {
            $rules[] = new EligibleCategoriesRule($configuration['eligible_categories']);
        }

        if (isset($configuration['first_order_only']) && $configuration['first_order_only'] === true) {
            $rules[] = new FirstOrderOnlyRule();
        }

        if (isset($configuration['user_usage_limit'])) {
            $rules[] = new UserUsageLimitRule($promoCode, (int) $configuration['user_usage_limit'], $this->repository);
        }

        if (isset($configuration['global_usage_limit'])) {
            $rules[] = new GlobalUsageLimitRule($promoCode, (int) $configuration['global_usage_limit'], $this->repository);
        }

        if (isset($configuration['global_amount_limit'])) {
            $rules[] = new GlobalAmountLimitRule($promoCode, (float) $configuration['global_amount_limit'], $this->repository);
        }

        if (isset($configuration['restricted_usage']) && $configuration['restricted_usage'] === true) {
            $rules[] = new RestrictedUsageRule($promoCode, $this->repository);
        }

        return $rules;
    }
}
