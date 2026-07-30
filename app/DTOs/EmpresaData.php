<?php

namespace App\DTOs;

class EmpresaData
{
    public function __construct(
        public readonly ?string $nombre,
        public readonly ?string $razon_social,
        public readonly ?string $ruc,
        public readonly ?string $direccion,
        public readonly ?string $telefono,
        public readonly ?string $correo,
        public readonly ?string $logo,
        public readonly ?string $sitio_web,
        public readonly ?int $estado = 1,
        public readonly ?int $creado_por = null,
    ) {}

    public static function fromArray(array $data): self
    {
        return new self(
            nombre: $data['nombre'] ?? null,
            razon_social: $data['razon_social'] ?? null,
            ruc: $data['ruc'] ?? null,
            direccion: $data['direccion'] ?? null,
            telefono: $data['telefono'] ?? null,
            correo: $data['correo'] ?? null,
            logo: $data['logo'] ?? null,
            sitio_web: $data['sitio_web'] ?? null,
            estado: $data['estado'] ?? 1,
            creado_por: $data['creado_por'],// ?? auth()->id(), //ver como traer el id de inicio sesion del usario
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
        ], fn($value) => $value !== null);
    }
}