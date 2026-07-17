<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * Contrato para jerarquías de categoría dentro del dominio.
 * Permite evaluar herencia (padre/hijo) para la regla `eligible_categories`
 * sin depender de modelos relacionales ni consultas SQL recursivas (ej. Eloquent/Stanza).
 */
interface CategoryInterface
{
    /**
     * Identificador único de la categoría.
     */
    public function getId(): string|int;

    /**
     * Verifica si esta categoría es exactamente o es descendiente (hijo/nieto)
     * de la categoría objetivo especificada por su ID.
     */
    public function isDescendantOfOrEquals(string|int $targetCategoryId): bool;
}
