<?php

declare(strict_types=1);

namespace App\Entities;

use App\Enums\PromoCodeStatus;
use DateTimeImmutable;

/**
 * Entidad de dominio inmutable que representa un código promocional (tabla `promo_codes`
 * en docs/diagrams/modelo_datos.mermaid). No contiene lógica de validación propia:
 * decidir si es elegible es responsabilidad de las reglas fijas y configurables
 * (Specification), no de la entidad.
 */
final readonly class PromoCode
{
    public function __construct(
        public string|int $id,
        public string $code,
        public string $type,
        public float $value,
        public PromoCodeStatus $status,
        public ?DateTimeImmutable $startsAt,
        public ?DateTimeImmutable $endsAt,
    ) {
    }
}
