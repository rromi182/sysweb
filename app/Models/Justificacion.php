<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Justificacion extends Model
{
    protected $table = 'justificaciones';

    protected $fillable = [
        'empleado_id',
        'empresa_id',
        'permiso_id',
        'asistencia_id',
        'tipo_justificacion',
        'motivo',
        'fecha_inicio',
        'fecha_fin',
        'archivo_documento',
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

    public function permiso(): BelongsTo
    {
        return $this->belongsTo(Permiso::class, 'permiso_id');
    }

    public function asistencia(): BelongsTo
    {
        return $this->belongsTo(Asistencia::class, 'asistencia_id');
    }

    public function aprobador(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'aprobado_por');
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

    public function scopeMedico($query)
    {
        return $query->where('tipo_justificacion', 'medico');
    }
}