<?php

declare(strict_types=1);

namespace App\ValueObjects;

use App\Contracts\OrderCollectionInterface;
use App\Contracts\OrderableInterface;

/**
 * Colección inmutable y tipada para manipular conjuntos de órdenes sin ORM.
 */
final readonly class OrderCollection implements OrderCollectionInterface
{
    /**
     * @var array<int|string, OrderableInterface>
     */
    private array $orders;

    /**
     * @param iterable<OrderableInterface> $orders
     */
    public function __construct(iterable $orders = [])
    {
        $items = [];
        foreach ($orders as $order) {
            $items[$order->getId()] = $order;
        }
        $this->orders = $items;
    }

    public function diff(OrderCollectionInterface $ordersToExclude): OrderCollectionInterface
    {
        $excludeIds = array_flip($ordersToExclude->getIds());
        $filtered = array_filter(
            $this->orders,
            fn(OrderableInterface $order) => !isset($excludeIds[$order->getId()])
        );

        return new self($filtered);
    }

    public function getIds(): array
    {
        return array_keys($this->orders);
    }

    public function isEmpty(): bool
    {
        return $this->orders === [];
    }

    public function count(): int
    {
        return \count($this->orders);
    }

    /**
     * @return \Traversable<int|string, OrderableInterface>
     */
    public function getIterator(): \Traversable
    {
        return new \ArrayIterator($this->orders);
    }
}
