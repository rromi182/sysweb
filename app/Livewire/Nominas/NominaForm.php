<?php

namespace App\Livewire\Nominas;

use Livewire\Component;
use App\Models\Empleado;
use App\Models\MovimientoNomina;
use App\Enums\TipoMovimientoEnum;
use App\DTOs\MovimientoNominaDTO;
use App\Services\NominaService;
use Illuminate\Support\Facades\Auth;

class NominaForm extends Component
{
    public $empleado_id = '';
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
    }

    public function updatedTipoMovimiento($value)
    {
        if ($value === 'extra') {
            $this->monto = 500000;
        }
        if ($value === 'sueldo') {
            $this->monto = 0; 
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
    }

    public function resetForm()
    {
        $this->reset(['empleado_id', 'tipo_movimiento', 'monto', 'observacion', 'movimientoId', 'modoEdicion']);
        $this->tipo_movimiento = 'sueldo';
        $this->fecha = now()->format('Y-m-d');
    }

    public function save(NominaService $service)
    {
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

        $dto = MovimientoNominaDTO::fromRequest(
            [
                'empleado_id' => $this->empleado_id,
                'fecha' => $this->fecha,
                'tipo_movimiento' => $this->tipo_movimiento,
                'monto' => $this->monto,
                'observacion' => $this->observacion,
                'anio' => $this->anio,
                'mes' => $this->mes,
            ],
            Auth::user()->empresa_id, 
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

        $this->dispatch('movimientoGuardado');
        $this->resetForm();

        session()->flash('message', $message);
    }

    

    public function render()
    {
        $empresaId = Auth::check() ? Auth::user()->empresa_id : 0;

        return view('livewire.nominas.nomina-form', [
            'empleados' => Empleado::with('persona')
                ->where('estado', 'activo')
                ->where('empresa_id', $empresaId)
                ->get(),
            'tipos' => TipoMovimientoEnum::cases(),
        ]);
    }
}
