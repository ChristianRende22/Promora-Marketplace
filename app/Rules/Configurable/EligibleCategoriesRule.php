<?php

namespace App\Rules\Configurable;

use App\Contracts\OrderableInterface;
use App\Contracts\RuleSpecificationInterface;
use App\Exceptions\RuleValidationException;

class EligibleCategoriesRule implements RuleSpecificationInterface
{
    /**
     * @param array<int|string> $eligibleCategoryIds Array of valid category IDs, including children.
     */
    public function __construct(private readonly array $eligibleCategoryIds)
    {
    }

    public function isSatisfiedBy(OrderableInterface $order): bool
    {
        $category = $order->getOrderContext()->category;
        
        $isEligible = false;
        foreach ($this->eligibleCategoryIds as $eligibleId) {
            if ($category->isDescendantOfOrEquals($eligibleId)) {
                $isEligible = true;
                break;
            }
        }

        if (!$isEligible) {
            throw new RuleValidationException('invalid_code', 'The order category is not eligible for this promo code.');
        }

        return true;
    }
}
