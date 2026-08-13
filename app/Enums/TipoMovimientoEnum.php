<?php

namespace App\Enums;

enum TipoMovimientoEnum: string
{
    case SUELDO = 'sueldo';
    case EXTRA = 'extra';
    case VALE = 'vale';
    case AUSENCIA = 'ausencia';
    case LLEGADA_TARDIA = 'llegada_tardia';
    case OTROS = 'otros';

    public function esIngreso(): bool
    {
        return in_array($this, [self::SUELDO, self::EXTRA]);
    }

    public function label(): string
    {
        return match ($this) {
            self::SUELDO => 'Sueldo',
            self::EXTRA => 'Extra',
            self::VALE => 'Vale',
            self::AUSENCIA => 'Ausencia',
            self::LLEGADA_TARDIA => 'Llegada Tardía',
            self::OTROS => 'Otros',
        };
    }

    public function color(): string
    {
        return match ($this) {
            self::SUELDO, self::EXTRA => 'green',
            self::VALE, self::AUSENCIA, self::LLEGADA_TARDIA => 'red',
            self::OTROS => 'gray',
        };
    }

    public function labelPowergridFilter(): string
    {
        return $this->label();
    }
}
