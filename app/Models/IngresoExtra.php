<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class IngresoExtra extends Model
{
    protected $table = 'ingresos_extras';

    protected $fillable = [
        'empleado_id',
        'empresa_id',
        'concepto_id',
        'fecha',
        'monto',
        'descripcion',
        'horas_extras',
        'periodo_inicio',
        'periodo_fin',
        'estado',
        'creado_por',
        'actualizado_por',
    ];

    protected $casts = [
        'fecha' => 'date',
        'monto' => 'decimal:2',
        'horas_extras' => 'integer',
        'periodo_inicio' => 'date',
        'periodo_fin' => 'date',
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

    public function concepto(): BelongsTo
    {
        return $this->belongsTo(ConceptoSalarial::class, 'concepto_id');
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
    public function scopeActivo($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopePagado($query)
    {
        return $query->where('estado', 'pagado');
    }

    public function scopePorPeriodo($query, $inicio, $fin)
    {
        return $query->whereBetween('periodo_inicio', [$inicio, $fin])
            ->orWhereBetween('periodo_fin', [$inicio, $fin]);
    }
}