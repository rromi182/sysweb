<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\SoftDeletes;


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
        'actualizado_por'
    ];

    protected $casts = [
        'fecha_ingreso' => 'date',
        'fecha_egreso' => 'date',
        'salario_base' => 'integer',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
        'deleted_at' => 'datetime',
    ];

    // Relaciones
    public function persona()
    {
        return $this->belongsTo(Persona::class);
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function empresa()
    {
        return $this->belongsTo(Empresa::class);
    }

    public function sucursal()
    {
        return $this->belongsTo(Sucursal::class);
    }

    public function departamento()
    {
        return $this->belongsTo(Departamento::class);
    }

    public function cargo()
    {
        return $this->belongsTo(Cargo::class);
    }

    public function tipoContrato()
    {
        return $this->belongsTo(TipoContrato::class);
    }

    public function horario()
    {
        return $this->belongsTo(HorarioLaboral::class);
    }

    public function jefeInmediato()
    {
        return $this->belongsTo(Empleado::class, 'jefe_inmediato_id');
    }

    public function subordinados()
    {
        return $this->hasMany(Empleado::class, 'jefe_inmediato_id');
    }

    public function creador()
    {
        return $this->belongsTo(User::class, 'creado_por');
    }

    public function actualizador()
    {
        return $this->belongsTo(User::class, 'actualizado_por');
    }

    // Scopes útiles
    /*public function scopeActivo($query)
    {
        return $query->where('estado', 'activo');
    }

    public function scopePorEmpresa($query, $empresaId)
    {
        return $query->where('empresa_id', $empresaId);
    }

    // Método para crear empleado con persona
    public static function crearConPersona(array $datosFuncionario, array $datosPersona)
    {
        // Crear la persona primero
        $persona = Persona::create($datosPersona);
        
        // Agregar el ID de la persona al array del funcionario
        $datosFuncionario['persona_id'] = $persona->id;
        
        // Crear el funcionario
        return self::create($datosFuncionario);
    }

    // Método para actualizar empleado y persona
    public function actualizarConPersona(array $datosFuncionario, array $datosPersona)
    {
        // Actualizar la persona
        $this->persona->update($datosPersona);
        
        // Actualizar el funcionario
        return $this->update($datosFuncionario);
    }

    // Accessors para nombre completo
    public function getNombreCompletoAttribute()
    {
        if ($this->persona) {
            return $this->persona->nombres . ' ' . $this->persona->apellidos;
        }
        return null;
    }*/
}