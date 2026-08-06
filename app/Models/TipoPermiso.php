<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoPermiso extends Model
{
    protected $table = 'tipos_permiso';

    protected $fillable = [
        'nombre',
        'descripcion',
        'dias_maximos',
        'con_goce_sueldo',
        'requiere_justificacion',
        'estado',
    ];

    public function permisos(): HasMany
    {
        return $this->hasMany(Permiso::class, 'tipo_permiso_id');
    }
}