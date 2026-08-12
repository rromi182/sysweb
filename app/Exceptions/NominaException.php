<?php

namespace App\Exceptions;

use Exception;

class NominaException extends Exception
{
    public static function salarioNegativo(float $neto): self
    {
        return new self('El salario neto no puede ser negativo: ' . number_format($neto, 0, ',', '.'), 422);
    }

    public static function liquidacionBloqueada(): self
    {
        return new self('La liquidación ya fue aprobada o pagada. No se pueden modificar movimientos.', 403);
    }

    public static function movimientoInvalido(string $tipo): self
    {
        return new self("El tipo de movimiento '{$tipo}' no es válido.", 400);
    }

    public static function observacionRequerida(): self
    {
        return new self('El campo observación es obligatorio para movimientos tipo OTROS.', 422);
    }

    public static function sinLiquidacionesPrevias(): self
    {
        return new self('No existen liquidaciones previas para calcular el promedio de aportes.', 422);
    }
}
