<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Support\Facades\Auth;

class Empleado extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'empleados';

    protected $fillable = [
        'persona_id',
        'user_id',
        'empresa_id',
        'sucursal_id',
        'departamento_id',
        'cargo_id',
        'codigo_empleado',
        'tipo_contrato_id',
        'horario_id',
        'fecha_ingreso',
        'fecha_egreso',
        'estado',
        'jefe_inmediato_id',
        'salario_base',
        'numero_ips',
        'profesion',
        'creado_por',
        'actualizado_por',
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'fecha_egreso' => 'date',
        'salario_base' => 'integer',
        'estado' => 'string',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Scopes
    public function scopeActivo($query)
    {
        return $query->where('estado', 'activo');
    }

    // Relaciones
    public function persona(): BelongsTo
    {
        //return $this->belongsTo(Persona::class, 'persona_id');
        return $this->belongsTo(Persona::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    public function empresa(): BelongsTo
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function sucursal(): BelongsTo
    {
        return $this->belongsTo(Sucursal::class, 'sucursal_id');
    }

    public function departamento(): BelongsTo
    {
        return $this->belongsTo(Departamento::class, 'departamento_id');
    }

    public function cargo(): BelongsTo
    {
        return $this->belongsTo(Cargo::class, 'cargo_id');
    }

    public function tipoContrato(): BelongsTo
    {
        return $this->belongsTo(TipoContrato::class, 'tipo_contrato_id');
    }

    public function horario(): BelongsTo
    {
        return $this->belongsTo(HorarioLaboral::class, 'horario_id');
    }

    public function jefeInmediato(): BelongsTo
    {
        return $this->belongsTo(self::class, 'jefe_inmediato_id');
    }

    public function subordinados(): HasMany
    {
        return $this->hasMany(self::class, 'jefe_inmediato_id');
    }

    public function asistencias(): HasMany
    {
        return $this->hasMany(Asistencia::class, 'empleado_id');
    }

    public function permisos(): HasMany
    {
        return $this->hasMany(Permiso::class, 'empleado_id');
    }

    public function justificaciones(): HasMany
    {
        return $this->hasMany(Justificacion::class, 'empleado_id');
    }

    public function liquidaciones(): HasMany
    {
        return $this->hasMany(LiquidacionSalarial::class, 'empleado_id');
    }

    public function descuentos(): HasMany
    {
        return $this->hasMany(Descuento::class, 'empleado_id');
    }

    public function ingresosExtras(): HasMany
    {
        return $this->hasMany(IngresoExtra::class, 'empleado_id');
    }

    public function creador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function actualizador(): BelongsTo
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }

    // Accessors
    public function getNombreCompletoAttribute(): string
    {
        return $this->persona?->nombre_completo ?? 'N/A';
    }

    public function getDocumentoAttribute(): string
    {
        return $this->persona
            ? "{$this->persona->tipo_documento}-{$this->persona->numero_documento}"
            : 'N/A';
    }

    public function getEstadoBadgeAttribute(): string
    {
        $colores = [
            'activo' => 'bg-green-100 text-green-800',
            'vacaciones' => 'bg-blue-100 text-blue-800',
            'licencia' => 'bg-yellow-100 text-yellow-800',
            'suspendido' => 'bg-orange-100 text-orange-800',
            'inactivo' => 'bg-red-100 text-red-800',
        ];

        $clase = $colores[$this->estado] ?? 'bg-gray-100 text-gray-800';
        $label = ucfirst($this->estado);

        return "<span class=\"px-2 py-1 rounded-full text-xs font-medium {$clase}\">{$label}</span>";
    }

    protected static function boot()
    {
        parent::boot();

        static::creating(function ($model) {
            if (empty($model->creado_por)) {
                $model->creado_por = Auth::id();
            }
        });

        static::updating(function ($model) {
            $model->actualizado_por = Auth::id();
        });
    }
}