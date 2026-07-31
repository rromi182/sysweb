<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class EmpresaRequest extends FormRequest
{
    public function authorize(): bool
    {
        return true;
    }

    public function rules(): array
    {
        $empresaId = $this->route('empresa')?->id;

        return [
            'nombre' => ['required', 'string', 'max:100'],
            'razon_social' => ['nullable', 'string', 'max:100'],
            'ruc' => [
                'nullable',
                'string',
                'max:20',
                Rule::unique('empresas', 'ruc')->ignore($empresaId),
            ],
            'direccion' => ['nullable', 'string', 'max:255'],
            'telefono' => ['nullable', 'string', 'max:20'],
            'correo' => ['nullable', 'email', 'max:100'],
            'logo' => ['nullable', 'image', 'max:2048'], // 2MB max
            'sitio_web' => ['nullable', 'url', 'max:100'],
            'estado' => ['sometimes', 'boolean'],
        ];
    }

    public function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la empresa es obligatorio',
            'ruc.unique' => 'Este RUC ya está registrado en otra empresa',
            'correo.email' => 'El correo electrónico no es válido',
            'sitio_web.url' => 'La URL del sitio web no es válida',
            'logo.image' => 'El archivo debe ser una imagen',
            'logo.max' => 'La imagen no debe superar los 2MB',
        ];
    }
}