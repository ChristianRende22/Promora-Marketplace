<?php

declare(strict_types=1);

namespace App\Contracts;

/**
 * Contrato inmutable que representa una colección en memoria de órdenes del dominio.
 * Evita la dependencia de `Illuminate\Support\Collection` de Laravel.
 */
interface OrderCollectionInterface extends \Countable, \IteratorAggregate
{
    /**
     * Retorna una nueva colección excluyendo las órdenes cuyos IDs coincidan
     * con los IDs presentes en la colección proporcionada (`$ordersToExclude`).
     */
    public function diff(self $ordersToExclude): self;

    /**
     * Retorna los IDs únicos de las órdenes en esta colección.
     *
     * @return array<int, string|int>
     */
    public function getIds(): array;

    /**
     * Verifica si la colección está vacía.
     */
    public function isEmpty(): bool;

    /**
     * Retorna el conteo real de órdenes en la colección.
     */
    public function count(): int;
}
