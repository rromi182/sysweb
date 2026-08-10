<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class StoreEmpleadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'tipo_documento' => ['required', 'in:CI,RUC,PASAPORTE,DNI'],
            'numero_documento' => ['required', 'string', 'max:30', 'unique:personas,numero_documento'],
            'fecha_nacimiento' => ['nullable', 'date'],
            'sexo' => ['required', 'in:M,F'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'correo' => ['nullable', 'email', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:255'],

            'empresa_id' => ['required', 'exists:empresas,id'],
            'sucursal_id' => ['required', 'exists:sucursales,id'],
            'departamento_id' => ['nullable', 'exists:departamentos,id'],
            'cargo_id' => ['required', 'exists:cargos,id'],
            'codigo_empleado' => ['required', 'string', 'max:20', 'unique:empleados,codigo_empleado'],
            'tipo_contrato_id' => ['nullable', 'exists:tipos_contrato,id'],
            'horario_id' => ['nullable', 'exists:horarios_laborales,id'],
            'fecha_ingreso' => ['required', 'date'],
            'fecha_egreso' => ['nullable', 'date', 'after_or_equal:fecha_ingreso'],
            'jefe_inmediato_id' => ['nullable', 'exists:empleados,id'],
            'salario_base' => ['required', 'integer', 'min:0'],
            'numero_ips' => ['nullable', 'string', 'max:20'],
            'profesion' => ['nullable', 'string', 'max:100'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombres.required' => 'Los nombres son obligatorios.',
            'apellidos.required' => 'Los apellidos son obligatorios.',
            'numero_documento.required' => 'El documento es obligatorio.',
            'numero_documento.unique' => 'Este documento ya está registrado.',
            'empresa_id.required' => 'Debe seleccionar una empresa.',
            'sucursal_id.required' => 'Debe seleccionar una sucursal.',
            'cargo_id.required' => 'Debe seleccionar un cargo.',
            'codigo_empleado.required' => 'El código de empleado es obligatorio.',
            'codigo_empleado.unique' => 'Este código ya existe.',
            'fecha_ingreso.required' => 'La fecha de ingreso es obligatoria.',
            'salario_base.required' => 'El salario base es obligatorio.',
        ];
    }
}