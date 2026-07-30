<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Persona extends Model
{
    use SoftDeletes;

    protected $table = 'personas';

    protected $fillable = [
        'tipo_persona',
        'nombres',
        'apellidos',
        'tipo_documento',
        'numero_documento',
        'fecha_nacimiento',
        'sexo',
        'estado_civil',
        'nacionalidad',
        'direccion',
        'departamento',
        'ciudad',
        'telefono',
        'correo',
        'foto',
        'estado',
        'creado_por',
        'actualizado_por'
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'estado' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relaciones
    public function empleado()
    {
        return $this->hasOne(Empleado::class);
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function actualizador()
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }

    // Accessor para nombre completo
    public function getNombreCompletoAttribute()
    {
        return $this->nombres . ' ' . $this->apellidos;
    }

    // Scope para búsqueda por documento
    public function scopePorDocumento($query, $tipo, $numero)
    {
        return $query->where('tipo_documento', $tipo)
                    ->where('numero_documento', $numero);
    }
}