<?php

namespace App\Livewire\Nominas;

use Livewire\Component;
use App\Models\Empleado;
use App\Models\MovimientoNomina;


class TestTable extends Component
{
    public $search = '';
    public $perPage = 10;

    public function render()
    {
        $movimientos = MovimientoNomina::query()
            ->with('empleado')
            ->when($this->search, function ($query) {
                $query->whereHas('empleado', function ($q) {
                    $q->where('nombre', 'like', '%' . $this->search . '%')
                      ->orWhere('apellido', 'like', '%' . $this->search . '%');
                });
            })
            ->latest()
            ->paginate($this->perPage);

        return view('livewire.nominas.test-table', [
            'movimientos' => $movimientos
        ]);
    }
}