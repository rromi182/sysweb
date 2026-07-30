<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;

class Empresa extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'empresas';

    protected $fillable = [
        'nombre',
        'razon_social',
        'ruc',
        'direccion',
        'telefono',
        'correo',
        'logo',
        'sitio_web',
        'estado',  
        'creado_por',
        'actualizado_por',
    ];

    protected $casts = [
        'estado' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function actualizador()
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }

    /*public function getLogoUrlAttribute(): ?string
    {
        return $this->logo ? asset('storage/' . $this->logo) : null;
    }*/

   // Relaciones con otras entidades
    public function sucursales()
    {
        return $this->hasMany(Sucursal::class);
    }

    public function funcionarios()
    {
        return $this->hasMany(Empleado::class);
    }

    public function departamentos()
    {
        return $this->hasMany(Departamento::class);
    }

    public function cargos()
    {
        return $this->hasMany(Cargo::class);
    }

    // Scopes útiles
    public function scopeActivo($query)
    {
        return $query->where('estado', 1);
    }

    public function scopeBuscar($query, $termino)
    {
        return $query->where('nombre', 'LIKE', "%{$termino}%")
                    ->orWhere('ruc', 'LIKE', "%{$termino}%")
                    ->orWhere('razon_social', 'LIKE', "%{$termino}%");
    }

    // Accessors
    public function getNombreCompletoAttribute()
    {
        return $this->razon_social ?? $this->nombre;
    }

    public function getEstadoTextoAttribute()
    {
        return $this->estado ? 'Activo' : 'Inactivo';
    }

    public function getLogoUrlAttribute()
    {
        if ($this->logo) {
            return asset('storage/empresas/' . $this->logo);
        }
        return asset('images/logo-tit-2.jpg');
    }

}