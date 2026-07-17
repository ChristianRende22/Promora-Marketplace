<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * Representa el perfil de dominio del comprador para la evaluación de reglas en el motor.
 * Se desacopla completamente del modelo `User` o `Buyer` de Eloquent y de la capa de persistencia.
 */
interface BuyerProfileInterface
{
    /**
     * Identificador único del comprador (requerido por las reglas `restricted_usage` y `user_usage_limit`).
     */
    public function getId(): string|int;

    /**
     * Indica si es la primera orden del comprador en la plataforma (requerido para `first_order_only`).
     */
    public function isFirstOrder(): bool;
}
