<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Asistencia extends Model
{
    use SoftDeletes;

    protected $table = 'asistencias';

    protected $fillable = [
        'empleado_id',
        'empresa_id',
        'fecha_laboral',
        'hora_entrada',
        'hora_salida',
        'horas_normales',
        'horas_extras',
        'estado',
        'observaciones',
        'fuente',
        'creado_por',
        'actualizado_por',
    ];

    protected $casts = [
        'fecha_laboral' => 'date',
        'hora_entrada' => 'datetime:H:i:s',
        'hora_salida' => 'datetime:H:i:s',
        'horas_normales' => 'decimal:2',
        'horas_extras' => 'decimal:2',
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

    public function justificaciones(): HasMany
    {
        return $this->hasMany(Justificacion::class, 'asistencia_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function actualizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }

    // Scopes útiles
    public function scopePresente($query)
    {
        return $query->where('estado', 'presente');
    }

    public function scopeAusente($query)
    {
        return $query->where('estado', 'ausente');
    }

    public function scopeEntreFechas($query, $fechaInicio, $fechaFin)
    {
        return $query->whereBetween('fecha_laboral', [$fechaInicio, $fechaFin]);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }
}