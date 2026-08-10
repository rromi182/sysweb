<?php

namespace App\Livewire\Empleados;

use App\DTOs\EmpleadoData;
use App\Exceptions\EmpleadoException;
use App\Models\Cargo;
use App\Models\Departamento;
use App\Models\Empleado;
use App\Models\Empresa;
use App\Models\HorarioLaboral;
use App\Models\Sucursal;
use App\Models\TipoContrato;
use App\Services\EmpleadoService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Livewire\Attributes\On;
use Livewire\Component;

class EmpleadoForm extends Component
{
    public string $nombres = '';
    public string $apellidos = '';
    public array $tiposDocumento = ['CI', 'RUC', 'PASAPORTE'];
    public array $sexos = ['M' => 'Masculino', 'F' => 'Femenino'];
    public string $numero_documento = '';
    public ?string $fecha_nacimiento = null;
    public ?string $telefono = null;
    public ?string $correo = null;
    public ?string $direccion = null;

    public ?int $empresa_id = null;
    public ?int $sucursal_id = null;
    public ?int $departamento_id = null;
    public ?int $cargo_id = null;
    public string $codigo_empleado = '';
    public ?int $tipo_contrato_id = null;
    public ?int $horario_id = null;
    public string $fecha_ingreso = '';
    public ?string $fecha_egreso = null;
    public ?int $jefe_inmediato_id = null;
    public int $salario_base = 0;
    public ?string $numero_ips = null;
    public ?string $profesion = null;

    public bool $isEditing = false;
    public ?Empleado $empleado = null;

    protected function rules(): array
    {
        $rules = [
            'nombres' => 'required|string|max:100',
            'apellidos' => 'required|string|max:100',
            'tiposDocumento' => 'required|in:CI,RUC,PASAPORTE',
            'numero_documento' => 'required|string|max:30',
            'fecha_nacimiento' => 'nullable|date',
            'sexos' => 'required|in:M,F',
            'telefono' => 'nullable|string|max:30',
            'correo' => 'nullable|email|max:100',
            'direccion' => 'nullable|string|max:255',
            'empresa_id' => 'required|exists:empresas,id',
            'sucursal_id' => 'required|exists:sucursales,id',
            'cargo_id' => 'required|exists:cargos,id',
            'codigo_empleado' => 'required|string|max:20',
            'tipo_contrato_id' => 'nullable|exists:tipos_contrato,id',
            'horario_id' => 'nullable|exists:horarios_laborales,id',
            'fecha_ingreso' => 'required|date',
            'fecha_egreso' => 'nullable|date|after_or_equal:fecha_ingreso',
            'jefe_inmediato_id' => 'nullable|exists:empleados,id',
            'salario_base' => 'required|integer|min:0',
            'numero_ips' => 'nullable|string|max:20',
            'profesion' => 'nullable|string|max:100',
        ];

        $uniqueCodigo = $this->isEditing && $this->empleado
            ? $this->empleado->id
            : 'NULL';

        $rules['codigo_empleado'] = 'required|string|max:20|unique:empleados,codigo_empleado,' . $uniqueCodigo;
        $rules['numero_documento'] = 'required|string|max:30|unique:personas,numero_documento,' . ($this->empleado?->persona_id ?? 'NULL');

        return $rules;
    }

    protected function messages(): array
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

    public function fillFields(Empleado $empleado): void
    {
        $persona = $empleado->persona;
        $this->nombres = $persona?->nombres ?? '';
        $this->apellidos = $persona?->apellidos ?? '';
        $this->tiposDocumento = $persona?->tipo_documento ?? 'CI';
        $this->numero_documento = $persona?->numero_documento ?? '';
        $this->fecha_nacimiento = $persona?->fecha_nacimiento?->format('Y-m-d');
        $this->sexos = $persona?->sexo ?? 'M';
        $this->telefono = $persona?->telefono;
        $this->correo = $persona?->correo;
        $this->direccion = $persona?->direccion;

        $this->empresa_id = $empleado->empresa_id;
        $this->sucursal_id = $empleado->sucursal_id;
        $this->departamento_id = $empleado->departamento_id;
        $this->cargo_id = $empleado->cargo_id;
        $this->codigo_empleado = $empleado->codigo_empleado;
        $this->tipo_contrato_id = $empleado->tipo_contrato_id;
        $this->horario_id = $empleado->horario_id;
        $this->fecha_ingreso = $empleado->fecha_ingreso?->format('Y-m-d');
        $this->fecha_egreso = $empleado->fecha_egreso?->format('Y-m-d');
        $this->jefe_inmediato_id = $empleado->jefe_inmediato_id;
        $this->salario_base = $empleado->salario_base;
        $this->numero_ips = $empleado->numero_ips;
        $this->profesion = $empleado->profesion;
    }

    // ═══════════════════════════════════════════════════════════════
    // SAVE: ahora delega TODO al Service + DTO
    // ═══════════════════════════════════════════════════════════════
    public function save(EmpleadoService $service): void
    {
        $validated = $this->validate();

        try {
            if ($this->isEditing && $this->empleado) {
                // ─── ACTUALIZAR ───
                $data = EmpleadoData::fromArray([
                    ...$validated,
                    'persona_id' => $this->empleado->persona_id,
                    'estado' => $this->empleado->estado,
                ], Auth::id());

                $service->updateEmpleado($this->empleado, $data);

                $this->dispatch('empleadoUpdated');
                $message = 'Empleado actualizado exitosamente';

            } else {
                // ─── CREAR ───
                $data = EmpleadoData::fromArray([
                    ...$validated,
                    'estado' => 'activo',
                ], Auth::id());

                $service->createEmpleado($data);

                $this->dispatch('empleadoCreated');
                $message = 'Empleado creado exitosamente';
            }

            $this->dispatch('toast', message: $message, type: 'success');
            $this->dispatch('close-modal', name: 'empleado-form-modal');
            $this->resetForm();

        } catch (EmpleadoException $e) {
            $this->dispatch('toast', message: $e->getMessage(), type: 'error');

        } catch (\Exception $e) {
            Log::error('Error saving empleado: ' . $e->getMessage());
            $this->dispatch('toast', message: 'Error inesperado. Contacte al administrador.', type: 'error');
        }
    }

    public function resetForm(): void
    {
        $this->reset([
            'nombres', 'apellidos', 'tiposDocumento', 'numero_documento',
            'fecha_nacimiento', 'sexos', 'telefono', 'correo', 'direccion',
            'empresa_id', 'sucursal_id', 'departamento_id', 'cargo_id',
            'codigo_empleado', 'tipo_contrato_id', 'horario_id',
            'fecha_ingreso', 'fecha_egreso', 'jefe_inmediato_id',
            'salario_base', 'numero_ips', 'profesion'
        ]);
        $this->isEditing = false;
        $this->empleado = null;
        $this->resetValidation();
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->dispatch('close-modal', name: 'empleado-form-modal');
    }

    #[On('edit-empleado')]
    public function edit(Empleado $empleado): void
    {
        $this->resetForm();
        $this->empleado = $empleado;
        $this->isEditing = true;
        $this->fillFields($empleado);
        $this->dispatch('open-modal', name: 'empleado-form-modal');
    }

    // ═══════════════════════════════════════════════════════════════
    // CREATE: usa el Service para generar código
    // ═══════════════════════════════════════════════════════════════
    #[On('create-empleado')]
    public function create(EmpleadoService $service): void
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->codigo_empleado = $service->generateNextCode();
        $this->dispatch('open-modal', name: 'empleado-form-modal');
    }

    public function render()
    {
        return view('livewire.empleados.empleado-form', [
            'empresas' => Empresa::where('estado', 1)->orderBy('nombre')->get(),
            'sucursales' => $this->empresa_id
                ? Sucursal::where('empresa_id', $this->empresa_id)->where('estado', 1)->orderBy('nombre')->get()
                : collect(),
            'departamentos' => $this->empresa_id
                ? Departamento::where('empresa_id', $this->empresa_id)->where('estado', 1)->orderBy('nombre')->get()
                : collect(),
            'cargos' => $this->empresa_id
                ? Cargo::where('empresa_id', $this->empresa_id)->where('estado', 1)->orderBy('nombre')->get()
                : collect(),
            'tiposContrato' => $this->empresa_id
                ? TipoContrato::where('empresa_id', $this->empresa_id)->orderBy('nombre')->get()
                : collect(),
            'horarios' => $this->empresa_id
                ? HorarioLaboral::where('empresa_id', $this->empresa_id)->where('estado', 1)->orderBy('nombre')->get()
                : collect(),
            'jefes' => Empleado::with('persona')
                ->when($this->empleado?->id, fn($q) => $q->where('id', '!=', $this->empleado->id))
                ->activo()
                ->get(),
        ]);
    }
}