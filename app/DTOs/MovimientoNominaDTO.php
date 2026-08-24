<?php

namespace App\DTOs;

use App\Enums\TipoMovimientoEnum;
use Carbon\Carbon;

readonly class MovimientoNominaDTO
{
    public function __construct(
        public int $empleadoId,
        public int $empresaId,
        public Carbon $fecha,
        public TipoMovimientoEnum $tipo,
        public int $monto,
        public ?string $observacion,
        public int $anio,
        public int $mes,
        public bool $esIngreso,
        public ?int $creadoPor = null,
    ) {}

    public static function fromRequest(array $data, int $empresaId, int $userId): self
    {
        $tipo = TipoMovimientoEnum::from($data['tipo_movimiento']);

        //CONVIERTE EL MONTO A INTEGER CON VALIDACIÓN
        $monto = 0;
        if (isset($data['monto'])) {
            if (is_numeric($data['monto'])) {
                $monto = (int) $data['monto'];  // Cast a int
            } else {
                // Si es string, elimina cualquier caracter no numérico
                $monto = (int) preg_replace('/[^0-9.]/', '', $data['monto']);  // Cast a int
            }
        }

        if ($monto < 0) {
            $monto = 0;
        }

        return new self(
            empleadoId: (int) $data['empleado_id'],
            empresaId: $empresaId,
            fecha: Carbon::parse($data['fecha']),
            tipo: $tipo,
            monto: $monto,
            observacion: $data['observacion'] ?? null,
            anio: (int) $data['anio'],
            mes: (int) $data['mes'],
            esIngreso: $tipo->esIngreso(),
            creadoPor: $userId,
        );
    }

    public function toArray(): array
    {
        return [
            'empleado_id' => $this->empleadoId,
            'empresa_id' => $this->empresaId,
            'fecha' => $this->fecha->format('Y-m-d'),
            'tipo_movimiento' => $this->tipo->value,
            'monto' => $this->monto, // CASTEO EXPLÍCITO
            'observacion' => $this->observacion,
            'anio' => $this->anio,
            'mes' => $this->mes,
            'es_ingreso' => $this->esIngreso,
            'creado_por' => $this->creadoPor,
        ];
    }
}
