<?php
// app/DTOs/EmpresaData.php

namespace App\DTOs;

use Illuminate\Support\Facades\Auth;

class EmpresaData
{
    public function __construct(
        public string $nombre,
        public ?string $razon_social = null,
        public ?string $ruc = null,
        public ?string $direccion = null,
        public ?string $telefono = null,
        public ?string $correo = null,
        public mixed $logo = null,
        public ?string $sitio_web = null,
        public int $estado = 1,
        public ?int $creado_por = null,
        public ?int $actualizado_por = null,
    ) {
        // Asignar usuario autenticado si no se proporciona
        if ($this->creado_por === null) {
            $this->creado_por = Auth::id();
        }
        if ($this->actualizado_por === null) {
            $this->actualizado_por = Auth::id();
        }
    }

    public static function fromArray(array $data): self
    {
        return new self(
            nombre: $data['nombre'] ?? '',
            razon_social: $data['razon_social'] ?? null,
            ruc: $data['ruc'] ?? null,
            direccion: $data['direccion'] ?? null,
            telefono: $data['telefono'] ?? null,
            correo: $data['correo'] ?? null,
            logo: $data['logo'] ?? null,
            sitio_web: $data['sitio_web'] ?? null,
            estado: $data['estado'] ?? 1,
            creado_por: $data['creado_por'] ?? null,
            actualizado_por: $data['actualizado_por'] ?? null,
        );
    }

    public function toArray(): array
    {
        return array_filter([
            'nombre' => $this->nombre,
            'razon_social' => $this->razon_social,
            'ruc' => $this->ruc,
            'direccion' => $this->direccion,
            'telefono' => $this->telefono,
            'correo' => $this->correo,
            'logo' => $this->logo,
            'sitio_web' => $this->sitio_web,
            'estado' => $this->estado,
            'creado_por' => $this->creado_por,
            'actualizado_por' => $this->actualizado_por,
        ], fn($value) => $value !== null && $value !== '');
    }
}