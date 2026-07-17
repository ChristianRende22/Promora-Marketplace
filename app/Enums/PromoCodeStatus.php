<?php

declare(strict_types=1);

namespace App\Enums;

/**
 * Ciclo de vida del código promocional (TDR sección 2): draft -> active -> expired,
 * con desvío posible a paused.
 */
enum PromoCodeStatus: string
{
    case Draft = 'draft';
    case Active = 'active';
    case Paused = 'paused';
    case Expired = 'expired';
}
