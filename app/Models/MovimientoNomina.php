<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\Auth;
use App\Enums\TipoMovimientoEnum;

class MovimientoNomina extends Model
{
    protected $table = 'movimientos_nomina';

    protected $casts = [
        'tipo_movimiento' => TipoMovimientoEnum::class,
        'fecha' => 'date',
        'monto' => 'decimal:2',
        'es_ingreso' => 'boolean',
    ];

    protected $fillable = [
        'empleado_id',
        'empresa_id',
        'fecha',
        'tipo_movimiento',
        'monto',
        'observacion',
        'anio',
        'mes',
        'es_ingreso',
        'estado',
        'creado_por',
        'actualizado_por',
    ];

    public function empleado()
    {
        return $this->belongsTo(Empleado::class);
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function scopeActivos($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopePorPeriodo($query, int $anio, int $mes)
    {
        return $query->where('anio', $anio)->where('mes', $mes);
    }

    public function scopePorEmpleado($query, int $empleadoId)
    {
        return $query->where('empleado_id', $empleadoId);
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->creado_por = $model->creado_por ?? Auth::id();
            $model->empresa_id = $model->empresa_id ?? Auth::user()?->empresa_id;
            $model->es_ingreso = $model->es_ingreso ?? $model->tipo_movimiento?->esIngreso();
        });

        static::updating(function ($model) {
            $model->actualizado_por = Auth::id();
        });
    }
}
