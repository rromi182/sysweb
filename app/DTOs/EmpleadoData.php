<?php

namespace App\DTOs;

class EmpleadoData
{
    public function __construct(
        // ─── Persona ───
        public readonly string $nombres,
        public readonly string $apellidos,
        public readonly string $tipo_documento,
        public readonly string $numero_documento,
        public readonly ?string $fecha_nacimiento,
        public readonly string $sexo,
        public readonly ?string $telefono,
        public readonly ?string $correo,
        public readonly ?string $direccion,

        // ─── Empleado ───
        public readonly int $empresa_id,
        public readonly int $sucursal_id,
        public readonly ?int $departamento_id,
        public readonly int $cargo_id,
        public readonly string $codigo_empleado,
        public readonly ?int $tipo_contrato_id,
        public readonly ?int $horario_id,
        public readonly string $fecha_ingreso,
        public readonly ?string $fecha_egreso,
        public readonly ?int $jefe_inmediato_id,
        public readonly int $salario_base,
        public readonly ?string $numero_ips,
        public readonly ?string $profesion,

        // ─── Metadata ───
        public readonly ?int $persona_id = null,
        public readonly ?int $user_id = null,
        public readonly ?string $estado = 'activo',
        public readonly ?int $creado_por = null,
        public readonly ?int $actualizado_por = null,
    ) {}

    public static function fromArray(array $data, ?int $userId = null): self
    {
        return new self(
            nombres: $data['nombres'],
            apellidos: $data['apellidos'],
            tipo_documento: $data['tipo_documento'],
            numero_documento: $data['numero_documento'],
            fecha_nacimiento: $data['fecha_nacimiento'] ?? null,
            sexo: $data['sexo'],
            telefono: $data['telefono'] ?? null,
            correo: $data['correo'] ?? null,
            direccion: $data['direccion'] ?? null,

            empresa_id: (int) $data['empresa_id'],
            sucursal_id: (int) $data['sucursal_id'],
            departamento_id: !empty($data['departamento_id']) ? (int) $data['departamento_id'] : null,
            cargo_id: (int) $data['cargo_id'],
            codigo_empleado: $data['codigo_empleado'],
            tipo_contrato_id: !empty($data['tipo_contrato_id']) ? (int) $data['tipo_contrato_id'] : null,
            horario_id: !empty($data['horario_id']) ? (int) $data['horario_id'] : null,
            fecha_ingreso: $data['fecha_ingreso'],
            fecha_egreso: $data['fecha_egreso'] ?? null,
            jefe_inmediato_id: !empty($data['jefe_inmediato_id']) ? (int) $data['jefe_inmediato_id'] : null,
            salario_base: (int) $data['salario_base'],
            numero_ips: $data['numero_ips'] ?? null,
            profesion: $data['profesion'] ?? null,

            persona_id: $data['persona_id'] ?? null,
            user_id: $data['user_id'] ?? null,
            estado: $data['estado'] ?? 'activo',
            creado_por: $userId,
            actualizado_por: $userId,
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

    public function personaArray(): array
    {
        return [
            'tipo_persona' => 'FISICA',
            'nombres' => $this->nombres,
            'apellidos' => $this->apellidos,
            'tipo_documento' => $this->tipo_documento,
            'numero_documento' => $this->numero_documento,
            'fecha_nacimiento' => $this->fecha_nacimiento,
            'sexo' => $this->sexo,
            'telefono' => $this->telefono,
            'correo' => $this->correo,
            'direccion' => $this->direccion,
            'estado' => 1,
            'creado_por' => $this->creado_por,
            'actualizado_por' => $this->actualizado_por,
        ];
    }
}