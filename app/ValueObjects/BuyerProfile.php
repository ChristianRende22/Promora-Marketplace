<?php

declare(strict_types=1);

namespace App\ValueObjects;

use App\Contracts\BuyerProfileInterface;
use App\Contracts\OrderCollectionInterface;

/**
 * Value Object inmutable que representa el estado relevante del comprador.
 */
final readonly class BuyerProfile implements BuyerProfileInterface
{
    public function __construct(
        private string|int $id,
        private bool $isFirstOrder,
        private OrderCollectionInterface $paidOrdersHistory
    ) {
    }

    public function getId(): string|int
    {
        return $this->id;
    }

    public function isFirstOrder(): bool
    {
        return $this->isFirstOrder;
    }

    public function getPaidOrdersHistory(): OrderCollectionInterface
    {
        return $this->paidOrdersHistory;
    }

    public function getQualifyingPaidOrdersCount(OrderCollectionInterface $currentOrdersInProcess): int
    {
        // Excluimos las órdenes actualmente en carrito/transacción en curso
        // para evitar falsos positivos en el conteo de tramos o límites consumidos.
        return $this->paidOrdersHistory->diff($currentOrdersInProcess)->count();
    }
}
