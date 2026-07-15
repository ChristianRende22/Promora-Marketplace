<?php

declare(strict_types=1);

namespace App\ValueObjects;

use App\Contracts\BuyerProfileInterface;
use App\Contracts\CategoryInterface;
use App\Contracts\OrderCollectionInterface;

/**
 * Objeto de Valor inmutable (Readonly Value Object) del dominio.
 * Su única responsabilidad (SRP) es PORTAR la información contextual requerida por las reglas.
 *
 * Las decisiones condicionales y de validación son responsabilidad exclusiva
 * de las especificaciones concretas del Patrón Specification (`RuleSpecificationInterface`),
 * y el acceso a datos históricos es responsabilidad del `PromoCodeRepositoryInterface`.
 */
final readonly class OrderContext
{
    public function __construct(
        public BuyerProfileInterface $buyerProfile,
        public CategoryInterface $category,
        public OrderCollectionInterface $currentOrders
    ) {
    }
}
