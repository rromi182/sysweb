<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Support\Facades\Auth;

class ParametroIPS extends Model
{
    protected $table = 'parametros_ips';

    protected $fillable = [
        'empresa_id',
        'anio',
        'mes',
        'aporte_empleado',
        'aporte_empleador',
        'salario_minimo',
        'aporte_fondo_pension',
        'aporte_seguro_salud',
        'creado_por',
        'actualizado_por',
    ];

    protected $casts = [
        'anio' => 'integer',
        'mes' => 'integer',
        'aporte_empleado' => 'integer',
        'aporte_empleador' => 'integer',
        'salario_minimo' => 'integer',
        'aporte_fondo_pension' => 'integer',
        'aporte_seguro_salud' => 'integer',
    ];

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class);
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function actualizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            $model->creado_por = $model->creado_por ?? Auth::id();
            $model->empresa_id = $model->empresa_id ?? Auth::user()?->empresa_id;
        });

        static::updating(function ($model) {
            $model->actualizado_por = Auth::id();
        });
    }

    public function scopePorPeriodo($query, int $anio, int $mes)
    {
        return $query->where('anio', $anio)->where('mes', $mes);
    }

    public function scopeVigente($query, int $anio, int $mes)
    {
        return $query->porPeriodo($anio, $mes)
            ->where('empresa_id', Auth::user()?->empresa_id);
    }
}
