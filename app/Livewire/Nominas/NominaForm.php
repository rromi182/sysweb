<?php

namespace App\Livewire\Nominas;

use Livewire\Attributes\On;
use Livewire\Component;
use App\Models\Empleado;
use App\Models\Empresa;
use App\Models\MovimientoNomina;
use App\Enums\TipoMovimientoEnum;
use App\DTOs\MovimientoNominaDTO;
use App\Services\NominaService;
use Illuminate\Support\Facades\Auth;

class NominaForm extends Component
{
    public $empleados = [];
    public $empleado_id = '';
    public $buscarEmpleado = '';
    public $mostrarDropdown = false;
    public $fecha;
    public $tipo_movimiento = 'sueldo';
    public $monto = 0;
    public $observacion = '';
    public $anio;
    public $mes;
    public $movimientoId = null;
    public $modoEdicion = false;

    protected $listeners = ['editarMovimiento', 'resetForm'];

    public function mount()
    {
        $this->anio = now()->year;
        $this->mes = now()->month;
        $this->fecha = now()->format('Y-m-d');
        $this->empleados = Empleado::with('persona')->get();
        $this->cargarEmpleados();
    }

    //Abrir modal en modo creación (resetea todo)
    #[On('crear-movimiento')]
    public function crearMovimiento(): void
    {
        $this->resetForm();
        $this->dispatch('open-modal', name: 'nomina-form-modal');
    }

    public function updated($propertyName)
    {
        if ($propertyName === 'tipo_movimiento') {
            if ($this->tipo_movimiento === 'extra') {
                $this->monto = 500000;
            } elseif ($this->tipo_movimiento === 'sueldo') {
                $this->monto = 3044000;
            } else {
                $this->monto = 0;
            }
        }

        if ($propertyName === 'buscarEmpleado') {
            $this->cargarEmpleados();
        }
    }

    public function editarMovimiento($id)
    {
        $mov = MovimientoNomina::findOrFail($id);
        $this->movimientoId = $mov->id;
        $this->empleado_id = $mov->empleado_id;
        $this->fecha = $mov->fecha->format('Y-m-d');
        $this->tipo_movimiento = $mov->tipo_movimiento->value;
        $this->monto = $mov->monto;
        $this->observacion = $mov->observacion ?? '';
        $this->anio = $mov->anio;
        $this->mes = $mov->mes;
        $this->modoEdicion = true;

        // Cargar el empleado seleccionado en el buscador
        $empleado = Empleado::with('persona')->find($mov->empleado_id);
        if ($empleado) {
            $this->buscarEmpleado = $empleado->persona->nombres . ' ' . $empleado->persona->apellidos . ' (' . $empleado->persona->numero_documento . ')';
        }

        $this->dispatch('open-modal', name: 'nomina-form-modal');
    }

    public function resetForm()
    {
        $this->reset(['empleado_id', 'buscarEmpleado', 'tipo_movimiento', 'monto', 'observacion', 'movimientoId', 'modoEdicion']);
        $this->tipo_movimiento = 'sueldo';
        $this->fecha = now()->format('Y-m-d');
        $this->mostrarDropdown = false;
    }

    public function save(NominaService $service)
    {
        $this->monto = (float) $this->monto;

        $this->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'fecha' => 'required|date',
            'tipo_movimiento' => 'required',
            'monto' => 'required|numeric|min:0',
            'observacion' => 'required_if:tipo_movimiento,otros',
            'anio' => 'required|integer',
            'mes' => 'required|integer|min:1|max:12',
        ], [
            'observacion.required_if' => 'La observación es obligatoria para movimientos tipo OTROS.',
        ]);

        if (!Auth::check()) {
            session()->flash('error', 'Usuario no autenticado');
            return;
        }

        $user = Auth::user();

        $empresaId = null;

        if (method_exists($user, 'empresas') && $user->empresas()->exists()) {
            $empresaId = $user->empresas()->first()->id;
        }

        if (!$empresaId && $this->empleado_id) {
            $empleado = Empleado::with('empresa')->find($this->empleado_id);
            if ($empleado && $empleado->empresa_id) {
                $empresaId = $empleado->empresa_id;
            }
        }

        if (!$empresaId) {
            $empresaId = Empresa::first()->id ?? null;
        }

        if (!$empresaId) {
            session()->flash('error', 'No se pudo determinar la empresa.');
            return;
        }

        $dto = MovimientoNominaDTO::fromRequest(
            [
                'empleado_id' => $this->empleado_id,
                'fecha' => $this->fecha,
                'tipo_movimiento' => $this->tipo_movimiento,
                'monto' => (float) $this->monto,
                'observacion' => $this->observacion,
                'anio' => $this->anio,
                'mes' => $this->mes,
            ],
            $empresaId,
            Auth::id()
        );

        if ($this->modoEdicion && $this->movimientoId) {
            $mov = MovimientoNomina::find($this->movimientoId);
            $mov->update($dto->toArray());
            $message = 'Movimiento actualizado correctamente';
        } else {
            $service->registrarMovimiento($dto);
            $message = 'Movimiento registrado correctamente';
        }

        $this->resetForm();

        //PATRÓN DEL PROYECTO: cerrar modal, refrescar tabla y mostrar toast
        $this->dispatch('close-modal', name: 'nomina-form-modal');
        $this->dispatch('pg:eventRefresh-nomina-table');
        $this->dispatch('toast', message: $message, type: 'success');
    }

    public function cargarEmpleados()
    {
        $query = Empleado::with('persona')->where('estado', 'activo');

        if (!empty($this->buscarEmpleado)) {
            $query->whereHas('persona', function ($q) {
                $q->where('nombres', 'LIKE', '%' . $this->buscarEmpleado . '%')
                    ->orWhere('apellidos', 'LIKE', '%' . $this->buscarEmpleado . '%')
                    ->orWhere('numero_documento', 'LIKE', '%' . $this->buscarEmpleado . '%');
            });
        }

        $this->empleados = $query->limit(20)->get();
        $this->mostrarDropdown = true;
    }

    public function seleccionarEmpleado($id)
    {
        $empleado = Empleado::with('persona')->find($id);
        if ($empleado) {
            $this->empleado_id = $id;
            $this->buscarEmpleado = $empleado->persona->nombres . ' ' . $empleado->persona->apellidos . ' (' . $empleado->persona->numero_documento . ')';
            $this->mostrarDropdown = false;
        }
    }

    public function ocultarDropdown()
    {
        $this->mostrarDropdown = false;
    }

    public function render()
    {
        return view('livewire.nominas.nomina-form', [
            'tipos' => TipoMovimientoEnum::cases(),
        ]);
    }
}