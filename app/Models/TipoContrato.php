<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class TipoContrato extends Model
{
    use SoftDeletes;

    protected $table = 'tipos_contrato'; // ← CORREGIDO: guión bajo

    protected $fillable = [
        'empresa_id',
        'nombre',
        'codigo',
        'descripcion',
        'duracion_defecto',
        'es_indefinido',
        'creado_por',
        'actualizado_por',
    ];

    protected $casts = [
        'es_indefinido' => 'boolean',
        'creado_en' => 'datetime',
        'actualizado_en' => 'datetime',
        'eliminado_en' => 'datetime',
    ];

    // Laravel busca created_at/updated_at por defecto, pero tu tabla usa nombres en español
    const CREATED_AT = 'creado_en';
    const UPDATED_AT = 'actualizado_en';

    // SoftDeletes usa deleted_at por defecto, pero tu columna se llama eliminado_en
    protected $dates = ['eliminado_en'];

    public function getDeletedAtColumn()
    {
        return 'eliminado_en';
    }

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
}