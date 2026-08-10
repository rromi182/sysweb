<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateEmpleadoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $empleado = $this->route('empleado');

        return [
            'nombres' => ['required', 'string', 'max:100'],
            'apellidos' => ['required', 'string', 'max:100'],
            'tipo_documento' => ['required', 'in:CI,RUC,PASAPORTE,DNI'],
            'numero_documento' => [
                'required',
                'string',
                'max:30',
                Rule::unique('personas', 'numero_documento')->ignore($empleado?->persona_id),
            ],
            'fecha_nacimiento' => ['nullable', 'date'],
            'sexo' => ['required', 'in:M,F'],
            'telefono' => ['nullable', 'string', 'max:30'],
            'correo' => ['nullable', 'email', 'max:100'],
            'direccion' => ['nullable', 'string', 'max:255'],

            'empresa_id' => ['required', 'exists:empresas,id'],
            'sucursal_id' => ['required', 'exists:sucursales,id'],
            'departamento_id' => ['nullable', 'exists:departamentos,id'],
            'cargo_id' => ['required', 'exists:cargos,id'],
            'codigo_empleado' => [
                'required',
                'string',
                'max:20',
                Rule::unique('empleados', 'codigo_empleado')->ignore($empleado?->id),
            ],
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
        return (new StoreEmpleadoRequest())->messages();
    }
}