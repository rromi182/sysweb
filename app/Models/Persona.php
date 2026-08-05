<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Persona extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'personas';

    protected $fillable = [
        'tipo_persona', 'nombres', 'apellidos', 'tipo_documento',
        'numero_documento', 'fecha_nacimiento', 'sexo', 'estado_civil',
        'nacionalidad', 'direccion', 'departamento', 'ciudad',
        'telefono', 'correo', 'foto', 'estado',
        'creado_por', 'actualizado_por',
    ];

    protected $casts = [
        'fecha_nacimiento' => 'date',
        'estado' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    protected $appends = ['nombre_completo'];

    public function empleados(): HasMany
    {
        return $this->hasMany(Empleado::class, 'persona_id');
    }

    public function getNombreCompletoAttribute(): string
    {
        return "{$this->nombres} {$this->apellidos}";
    }
}