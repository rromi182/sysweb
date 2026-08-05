<?php

namespace App\Livewire\Empleados;

use App\Models\Empleado;
use Livewire\Attributes\On;
use Livewire\Component;

class EmpleadoDetail extends Component
{
    public ?Empleado $empleado = null;

    #[On('show-empleado')]
    public function show(int $empleadoId): void
    {
        $this->empleado = Empleado::with([
            'persona', 'empresa', 'sucursal', 'departamento',
            'cargo', 'tipoContrato', 'horario', 'jefeInmediato.persona',
            'creador', 'actualizador'
        ])->find($empleadoId);

        if ($this->empleado) {
            $this->dispatch('open-modal', name: 'empleado-detail-modal');
        } else {
            $this->dispatch('toast', message: 'Empleado no encontrado', type: 'error');
        }
    }

    #[On('close-detail')]
    public function closeModal(): void
    {
        $this->empleado = null;
        $this->dispatch('close-modal', name: 'empleado-detail-modal');
    }

    public function render()
    {
        return view('livewire.empleados.empleado-detail');
    }
}