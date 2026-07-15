<?php

declare(strict_types=1);

namespace App\Contracts;

use App\ValueObjects\OrderContext;

/**
 * Puerto de entrada del dominio (OrderableInterface).
 * Cualquier entidad (modelo Eloquent, carrito de compras efímero o fake de pruebas)
 * que requiera validación o cálculo de descuento DEBE implementar este contrato.
 */
interface OrderableInterface
{
    /**
     * Obtiene el identificador único de la entidad comprable.
     */
    public function getId(): string|int;

    /**
     * Devuelve el monto subtotal sobre el cual el motor evaluará el `min_purchase_amount`
     * y calculará los descuentos proporcionales (percent o tiered).
     */
    public function getSubtotal(): float;

    /**
     * Devuelve el contexto completo (comprador, categoría, órdenes concurrentes en proceso)
     * requerido por el motor y sus especificaciones para validar las reglas de negocio.
     */
    public function getOrderContext(): OrderContext;
}
