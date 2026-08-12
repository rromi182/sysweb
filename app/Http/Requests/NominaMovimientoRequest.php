<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use App\Enums\TipoMovimientoEnum;

class NominaMovimientoRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        return [
            'empleado_id' => 'required|exists:empleados,id',
            'fecha' => 'required|date',
            'tipo_movimiento' => 'required|string|in:' . implode(',', array_column(TipoMovimientoEnum::cases(), 'value')),
            'monto' => 'required|numeric|min:0',
            'observacion' => 'nullable|string|max:500|required_if:tipo_movimiento,otros',
            'anio' => 'required|integer|min:2020|max:2100',
            'mes' => 'required|integer|min:1|max:12',
        ];
    }

    public function messages(): array
    {
        return [
            'observacion.required_if' => 'La observación es obligatoria cuando el tipo de movimiento es OTROS.',
            'empleado_id.exists' => 'El empleado seleccionado no existe.',
            'monto.min' => 'El monto debe ser mayor o igual a 0.',
        ];
    }
}
