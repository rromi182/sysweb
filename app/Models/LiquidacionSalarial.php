<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class LiquidacionSalarial extends Model
{
    protected $table = 'liquidaciones_salariales';

    protected $fillable = [
        'empleado_id',
        'empresa_id',
        'periodo_anio',
        'periodo_mes',
        'tipo',
        'salario_base',
        'total_ingresos',
        'total_descuentos',
        'total_aportes_ips',
        'total_neto',
        'estado',
        'fecha_calculo',
        'fecha_aprobacion',
        'fecha_pago',
        'observaciones',
        'creado_por',
        'actualizado_por',
    ];

    protected $casts = [
        'periodo_mes' => 'integer',
        'periodo_anio' => 'integer',
        'salario_base' => 'decimal:2',
        'total_ingresos' => 'decimal:2',
        'total_descuentos' => 'decimal:2',
        'total_aportes_ips' => 'decimal:2',
        'total_neto' => 'decimal:2',
        'fecha_calculo' => 'datetime',
        'fecha_aprobacion' => 'datetime',
        'fecha_pago' => 'date',
    ];

    // ============ RELACIONES ============
    public function empleado(): BelongsTo
    {
        return $this->belongsTo(Empleado::class, 'empleado_id');
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function detalles(): HasMany
    {
        return $this->hasMany(LiquidacionSalarialDetalle::class, 'liquidacion_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function actualizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }

    // ============ SCOPES ============
    public function scopeBorrador($query)
    {
        return $query->where('estado', 'borrador');
    }

    public function scopeCalculado($query)
    {
        return $query->where('estado', 'calculado');
    }

    public function scopeAprobado($query)
    {
        return $query->where('estado', 'aprobado');
    }

    public function scopePagado($query)
    {
        return $query->where('estado', 'pagado');
    }

    public function scopeAnulado($query)
    {
        return $query->where('estado', 'anulado');
    }

    public function scopePorPeriodo($query, $anio, $mes)
    {
        return $query->where('periodo_anio', $anio)
            ->where('periodo_mes', $mes);
    }

    public function scopePorEmpleado($query, $empleadoId)
    {
        return $query->where('empleado_id', $empleadoId);
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    // ============ MÉTODOS HELPER ============
    public function getNombreMesAttribute()
    {
        $meses = [
            1 => 'Enero', 2 => 'Febrero', 3 => 'Marzo', 4 => 'Abril',
            5 => 'Mayo', 6 => 'Junio', 7 => 'Julio', 8 => 'Agosto',
            9 => 'Septiembre', 10 => 'Octubre', 11 => 'Noviembre', 12 => 'Diciembre'
        ];
        return $meses[$this->periodo_mes] ?? '';
    }

    public function getPeriodoAttribute()
    {
        return $this->nombre_mes . ' ' . $this->periodo_anio;
    }

    public function getTotalNetoFormateadoAttribute()
    {
        return number_format($this->total_neto, 0, ',', '.');
    }

    public function getEstadoColorAttribute()
    {
        $colores = [
            'borrador' => 'gray',
            'calculado' => 'blue',
            'aprobado' => 'green',
            'pagado' => 'success',
            'anulado' => 'red',
        ];
        return $colores[$this->estado] ?? 'gray';
    }

    public function getEstadoBadgeAttribute()
    {
        $badges = [
            'borrador' => 'bg-gray-100 text-gray-800',
            'calculado' => 'bg-blue-100 text-blue-800',
            'aprobado' => 'bg-green-100 text-green-800',
            'pagado' => 'bg-emerald-100 text-emerald-800',
            'anulado' => 'bg-red-100 text-red-800',
        ];
        return $badges[$this->estado] ?? 'bg-gray-100 text-gray-800';
    }

    public function getTotalIngresosAttribute($value)
    {
        return (float) $value;
    }

    public function getTotalDescuentosAttribute($value)
    {
        return (float) $value;
    }

    // ============ MÉTODOS DE NEGOCIO ============
    public function calcularTotales(): void
    {
        $ingresos = $this->detalles()->ingresos()->sum('monto');
        $descuentos = $this->detalles()->descuentos()->sum('monto');
        $aportes = $this->detalles()->aportes()->sum('monto');

        $this->total_ingresos = $ingresos;
        $this->total_descuentos = $descuentos;
        $this->total_aportes_ips = $aportes;
        $this->total_neto = $this->salario_base + $ingresos - $descuentos - $aportes;

        $this->save();
    }

    public function puedeAprobar(): bool
    {
        return $this->estado === 'calculado';
    }

    public function puedePagar(): bool
    {
        return $this->estado === 'aprobado';
    }

    public function aprobar(int $usuarioId): void
    {
        if (!$this->puedeAprobar()) {
            throw new \Exception('La liquidación no se puede aprobar en su estado actual.');
        }

        $this->estado = 'aprobado';
        $this->fecha_aprobacion = now();
        $this->actualizado_por = $usuarioId;
        $this->save();
    }

    public function pagar(): void
    {
        if (!$this->puedePagar()) {
            throw new \Exception('La liquidación no se puede pagar en su estado actual.');
        }

        $this->estado = 'pagado';
        $this->fecha_pago = now()->toDateString();
        $this->save();
    }

    public function anular(): void
    {
        if ($this->estado === 'pagado') {
            throw new \Exception('No se puede anular una liquidación ya pagada.');
        }

        $this->estado = 'anulado';
        $this->save();
    }
}

// ============================================================
// MODELO DETALLE (Clase anidada en el mismo archivo)
// ============================================================

class LiquidacionSalarialDetalle extends Model
{
    protected $table = 'liquidaciones_salariales_det';

    public $timestamps = false;

    protected $fillable = [
        'liquidacion_id',
        'concepto_id',
        'tipo',
        'monto',
        'cantidad',
        'formula_aplicada',
    ];

    protected $casts = [
        'monto' => 'decimal:2',
        'cantidad' => 'decimal:2',
    ];

    // ============ RELACIONES ============
    public function liquidacion(): BelongsTo
    {
        return $this->belongsTo(LiquidacionSalarial::class, 'liquidacion_id');
    }

    public function concepto(): BelongsTo
    {
        return $this->belongsTo(ConceptoSalarial::class, 'concepto_id');
    }

    // ============ SCOPES ============
    public function scopeIngresos($query)
    {
        return $query->where('tipo', 'ingreso');
    }

    public function scopeDescuentos($query)
    {
        return $query->where('tipo', 'descuento');
    }

    public function scopeAportes($query)
    {
        return $query->where('tipo', 'aporte');
    }

    public function scopePorConcepto($query, $conceptoId)
    {
        return $query->where('concepto_id', $conceptoId);
    }

    // ============ MÉTODOS HELPER ============
    public function getMontoFormateadoAttribute()
    {
        return number_format($this->monto, 0, ',', '.');
    }

    public function getSubtotalAttribute()
    {
        return $this->monto * $this->cantidad;
    }

    public function getTipoColorAttribute()
    {
        $colores = [
            'ingreso' => 'green',
            'descuento' => 'red',
            'aporte' => 'orange',
        ];
        return $colores[$this->tipo] ?? 'gray';
    }

    public function getTipoIconAttribute()
    {
        $iconos = [
            'ingreso' => 'heroicon-o-arrow-up-circle',
            'descuento' => 'heroicon-o-arrow-down-circle',
            'aporte' => 'heroicon-o-minus-circle',
        ];
        return $iconos[$this->tipo] ?? 'heroicon-o-circle';
    }
}