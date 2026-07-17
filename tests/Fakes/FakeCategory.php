<?php

namespace Tests\Fakes;

use App\Contracts\CategoryInterface;

class FakeCategory implements CategoryInterface
{
    public function __construct(private readonly string|int $id, private readonly array $ancestors = [])
    {
    }

    public function getId(): string|int
    {
        return $this->id;
    }

    public function isDescendantOfOrEquals(string|int $targetCategoryId): bool
    {
        if ($this->id === $targetCategoryId) {
            return true;
        }

        return in_array($targetCategoryId, $this->ancestors, true);
    }
}
