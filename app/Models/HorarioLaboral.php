<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class HorarioLaboral extends Model
{
    use SoftDeletes;

    protected $table = 'horarios-laborales';

    protected $fillable = [
        'nombre',
        'empresa_id',
        'nombre',
        'codigo',
        'tipo',
        'lunes_entrada',
        'lunes_salida',
        'martes_entrada',
        'martes_salida',
        'miercoles_entrada',
        'miercoles_salida',
        'jueves_entrada',
        'jueves_salida',
        'viernes_entrada',
        'viernes_salida',
        'sabado_entrada',
        'sabado_salida',
        'domingo_entrada',
        'domingo_salida',
        'duracion_pausa',
    ];

    protected $casts = [
        'estado' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function actualizador()
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class, 'empresa_id');
    }

    public function encargado()
    {
        return $this->belongsTo(Empleado::class, 'encargado_id');
    }

}