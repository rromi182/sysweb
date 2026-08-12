<?php

namespace App\DTOs;

class ParametroIPSDTO
{
    public function __construct(
        public readonly int $anio,
        public readonly int $mes,
        public readonly int $aporte_empleado,
        public readonly int $aporte_empleador,
        public readonly int $salario_minimo,
        public readonly int $aporte_fondo_pension = 0,
        public readonly int $aporte_seguro_salud = 0,
    ) {}

    public function toArray(): array
    {
        return [
            'anio' => $this->anio,
            'mes' => $this->mes,
            'aporte_empleado' => $this->aporte_empleado,
            'aporte_empleador' => $this->aporte_empleador,
            'salario_minimo' => $this->salario_minimo,
            'aporte_fondo_pension' => $this->aporte_fondo_pension,
            'aporte_seguro_salud' => $this->aporte_seguro_salud,
        ];
    }
}