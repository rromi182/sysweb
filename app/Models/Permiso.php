<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\SoftDeletes;

class Permiso extends Model
{
    use SoftDeletes;

    protected $table = 'permisos';

    protected $fillable = [
        'empleado_id',
        'empresa_id',
        'tipo_permiso_id',
        'fecha_inicio',
        'fecha_fin',
        'hora_inicio',
        'hora_fin',
        'duracion_dias',
        'motivo',
        'estado',
        'aprobado_por',
        'fecha_aprobacion',
        'observaciones',
        'creado_por',
        'actualizado_por',
    ];

    protected $casts = [
        'fecha_inicio' => 'date',
        'fecha_fin' => 'date',
        'hora_inicio' => 'datetime:H:i:s',
        'hora_fin' => 'datetime:H:i:s',
        'duracion_dias' => 'decimal:2',
        'fecha_aprobacion' => 'datetime',
    ];

    // Relaciones
    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function tipoPermiso(): BelongsTo
    {
        return $this->belongsTo(TipoPermiso::class, 'tipo_permiso_id');
    }

    public function aprobador(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'aprobado_por');
    }

    public function justificacion(): BelongsTo
    {
        return $this->belongsTo(Justificacion::class, 'permiso_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function actualizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }

    // Scopes
    public function scopePendiente($query)
    {
        return $query->where('estado', 'pendiente');
    }

    public function scopeAprobado($query)
    {
        return $query->where('estado', 'aprobado');
    }

    public function scopeRechazado($query)
    {
        return $query->where('estado', 'rechazado');
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'pendiente')
            ->orWhere('estado', 'aprobado');
    }
}