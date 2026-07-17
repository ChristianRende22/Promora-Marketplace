<?php

declare(strict_types=1);

namespace Tests\Factories;

use App\Entities\PromoCode;
use App\Enums\PromoCodeStatus;
use DateTimeImmutable;

/**
 * Factory de pruebas encadenable para construir PromoCode con estado controlado,
 * análogo a OrderContextFactory. Los defaults de new() ya son un código válido
 * (active, dentro de vigencia) para no repetir setup en cada test.
 */
class PromoCodeFactory
{
    private string|int $id = 1;
    private string $code = 'PROMO10';
    private string $type = 'fixed';
    private float $value = 10.0;
    private PromoCodeStatus $status = PromoCodeStatus::Active;
    private ?DateTimeImmutable $startsAt = null;
    private ?DateTimeImmutable $endsAt = null;

    public static function new(): self
    {
        $factory = new self();
        $factory->startsAt = new DateTimeImmutable('-1 day');
        $factory->endsAt = new DateTimeImmutable('+1 day');

        return $factory;
    }

    public function withStatus(PromoCodeStatus $status): self
    {
        $this->status = $status;

        return $this;
    }

    public function withCode(string $code): self
    {
        $this->code = $code;

        return $this;
    }

    public function withValidityPeriod(?DateTimeImmutable $startsAt, ?DateTimeImmutable $endsAt): self
    {
        $this->startsAt = $startsAt;
        $this->endsAt = $endsAt;

        return $this;
    }

    public function create(): PromoCode
    {
        return new PromoCode(
            $this->id,
            $this->code,
            $this->type,
            $this->value,
            $this->status,
            $this->startsAt,
            $this->endsAt,
        );
    }
}
