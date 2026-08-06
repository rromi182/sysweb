<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class ConceptoSalarial extends Model
{
    protected $table = 'conceptos_salariales';

    protected $fillable = [
        'nombre',
        'descripcion',
        'tipo', // ingreso, descuento, aporte
        'afecta_ips',
        'afecta_irp',
        'formula_calculo',
        'estado',
    ];

    public function descuentos(): HasMany
    {
        return $this->hasMany(Descuento::class, 'concepto_id');
    }

    public function ingresosExtras(): HasMany
    {
        return $this->hasMany(IngresoExtra::class, 'concepto_id');
    }

    public function liquidacionDetalles(): HasMany
    {
        return $this->hasMany(LiquidacionSalarialDetalle::class, 'concepto_id');
    }
}