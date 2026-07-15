<?php

namespace App\Domain\PromoCode\Rules;

use App\Domain\PromoCode\Contracts\OrderableInterface;
use App\Domain\PromoCode\Contracts\RuleSpecificationInterface;
use App\Domain\PromoCode\Exceptions\RuleValidationException;

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
        $categoryId = $order->getOrderContext()->categoryId;

        if (!in_array($categoryId, $this->eligibleCategoryIds, true)) {
            throw new RuleValidationException('invalid_code', 'The order category is not eligible for this promo code.');
        }

        return true;
    }
}
