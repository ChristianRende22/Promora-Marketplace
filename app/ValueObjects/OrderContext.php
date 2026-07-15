<?php

declare(strict_types=1);

namespace App\ValueObjects;

use App\Contracts\BuyerProfileInterface;
use App\Contracts\CategoryInterface;
use App\Contracts\OrderCollectionInterface;

/**
 * Objeto de Valor inmutable (Readonly Value Object) del dominio.
 * Transporta el contexto situacional completo requerido para evaluar las reglas
 * y aplicar la estrategia de cálculo correspondiente.
 */
final readonly class OrderContext
{
    public function __construct(
        public BuyerProfileInterface $buyerProfile,
        public CategoryInterface $category,
        public OrderCollectionInterface $currentOrders
    ) {
    }

    /**
     * Verifica si el comprador actual es elegible basándose en una lista de IDs permitidos (`restricted_usage`).
     *
     * @param array<int, string|int> $allowedUserIds
     */
    public function isBuyerAllowed(array $allowedUserIds): bool
    {
        return \in_array($this->buyerProfile->getId(), $allowedUserIds, true);
    }

    /**
     * Determina el conteo neto de órdenes históricas válidas del comprador para tramos (`tiered`)
     * o límites máximos de uso, excluyendo las órdenes transaccionales activas en la solicitud.
     */
    public function getQualifyingOrdersCount(): int
    {
        return $this->buyerProfile->getQualifyingPaidOrdersCount($this->currentOrders);
    }

    /**
     * Evalúa si la categoría de la orden satisface las categorías elegibles del cupón,
     * soportando herencia padre/hijo.
     *
     * @param array<int, string|int> $eligibleCategoryIds
     */
    public function isCategoryEligible(array $eligibleCategoryIds): bool
    {
        if ($eligibleCategoryIds === []) {
            return true;
        }

        foreach ($eligibleCategoryIds as $targetCategoryId) {
            if ($this->category->isDescendantOfOrEquals($targetCategoryId)) {
                return true;
            }
        }

        return false;
    }
}
