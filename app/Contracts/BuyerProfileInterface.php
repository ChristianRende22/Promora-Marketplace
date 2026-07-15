<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * Representa el perfil de dominio del comprador para evaluación de reglas.
 * Se desacopla completamente del modelo `User` o `Buyer` de Eloquent.
 */
interface BuyerProfileInterface
{
    /**
     * Identificador único del comprador (requerido por `restricted_usage` y `user_usage_limit`).
     */
    public function getId(): string|int;

    /**
     * Indica si el comprador no tiene órdenes históricas previas pagadas en la plataforma
     * (requerido para `first_order_only`).
     */
    public function isFirstOrder(): bool;

    /**
     * Obtiene el historial de órdenes efectivamente pagadas y no canceladas del usuario.
     */
    public function getPaidOrdersHistory(): OrderCollectionInterface;

    /**
     * Retorna la cantidad de órdenes pagadas elegibles para descuentos progresivos (`tiered`)
     * o límites de uso, excluyendo dinámicamente las órdenes transaccionales en proceso actual.
     */
    public function getQualifyingPaidOrdersCount(OrderCollectionInterface $currentOrdersInProcess): int;
}
