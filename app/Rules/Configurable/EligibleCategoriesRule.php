<?php

namespace App\Rules\Configurable;

use App\Contracts\OrderableInterface;
use App\Contracts\RuleSpecificationInterface;
use App\ValueObjects\ValidationResult;

class EligibleCategoriesRule implements RuleSpecificationInterface
{
    /**
     * @param array<int|string> $eligibleCategoryIds Array of valid category IDs, including children.
     */
    public function __construct(private readonly array $eligibleCategoryIds)
    {
    }

    public function isSatisfiedBy(OrderableInterface $order): ValidationResult
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
            return ValidationResult::failed('invalid_code');
        }

        return ValidationResult::success();
    }
}
