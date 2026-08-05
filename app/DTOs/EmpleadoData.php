<?php

namespace App\DTOs;

use Illuminate\Support\Facades\Auth;

class EmpleadoData
{
    public function __construct(
        public readonly int $persona_id,
        public readonly int $empresa_id,
        public readonly int $sucursal_id,
        public readonly int $cargo_id,
        public readonly string $codigo_empleado,
        public readonly string $fecha_ingreso,
        public readonly int $salario_base,
        public readonly ?int $user_id = null,
        public readonly ?int $departamento_id = null,
        public readonly ?int $tipo_contrato_id = null,
        public readonly ?int $horario_id = null,
        public readonly ?string $fecha_egreso = null,
        public readonly string $estado = 'activo',
        public readonly ?int $jefe_inmediato_id = null,
        public readonly ?string $numero_ips = null,
        public readonly ?string $profesion = null,
        public readonly ?int $creado_por = null,
        public readonly ?int $actualizado_por = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            persona_id: $data['persona_id'],
            empresa_id: $data['empresa_id'],
            sucursal_id: $data['sucursal_id'],
            cargo_id: $data['cargo_id'],
            codigo_empleado: $data['codigo_empleado'],
            fecha_ingreso: $data['fecha_ingreso'],
            salario_base: $data['salario_base'],
            user_id: $data['user_id'] ?? null,
            departamento_id: $data['departamento_id'] ?? null,
            tipo_contrato_id: $data['tipo_contrato_id'] ?? null,
            horario_id: $data['horario_id'] ?? null,
            fecha_egreso: $data['fecha_egreso'] ?? null,
            estado: $data['estado'] ?? 'activo',
            jefe_inmediato_id: $data['jefe_inmediato_id'] ?? null,
            numero_ips: $data['numero_ips'] ?? null,
            profesion: $data['profesion'] ?? null,
            creado_por: $data['creado_por'] ?? Auth::id(),
            actualizado_por: $data['actualizado_por'] ?? Auth::id(),
        );
    }

    public function toArray(): array
    {
        return [
            'persona_id' => $this->persona_id,
            'user_id' => $this->user_id,
            'empresa_id' => $this->empresa_id,
            'sucursal_id' => $this->sucursal_id,
            'departamento_id' => $this->departamento_id,
            'cargo_id' => $this->cargo_id,
            'codigo_empleado' => $this->codigo_empleado,
            'tipo_contrato_id' => $this->tipo_contrato_id,
            'horario_id' => $this->horario_id,
            'fecha_ingreso' => $this->fecha_ingreso,
            'fecha_egreso' => $this->fecha_egreso,
            'estado' => $this->estado,
            'jefe_inmediato_id' => $this->jefe_inmediato_id,
            'salario_base' => $this->salario_base,
            'numero_ips' => $this->numero_ips,
            'profesion' => $this->profesion,
            'creado_por' => $this->creado_por,
            'actualizado_por' => $this->actualizado_por,
        ];
    }
}