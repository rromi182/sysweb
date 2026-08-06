<?php

namespace App\Livewire\Dashboard;

use App\Models\Empleado;
use App\Models\Departamento;
use App\Models\TipoContrato;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;
use Livewire\Component;

class HrDashboard extends Component
{
    public string $search = '';
    public ?int $departamentoFilter = null;
    public ?int $tipoContratoFilter = null;
    public string $estadoFilter = 'activo';

    public function mount()
    {
        $this->estadoFilter = 'activo';
    }

    public function getStatsProperty(): array
    {
        $baseQuery = Empleado::query();

        return [
            'total_empleados' => (clone $baseQuery)->activo()->count(),
            'nuevos_este_mes' => (clone $baseQuery)
                ->whereMonth('fecha_ingreso', Carbon::now()->month)
                ->whereYear('fecha_ingreso', Carbon::now()->year)
                ->count(),
            'salario_promedio' => (clone $baseQuery)->activo()->avg('salario_base') ?? 0,
            'departamentos' => Departamento::count(),
            'por_departamento' => (clone $baseQuery)
                ->select('departamento_id', DB::raw('count(*) as total'))
                ->with('departamento')
                ->groupBy('departamento_id')
                ->orderByDesc('total')
                ->limit(6)
                ->get(),
            'por_contrato' => (clone $baseQuery)
                ->select('tipo_contrato_id', DB::raw('count(*) as total'))
                ->with('tipoContrato')
                ->groupBy('tipo_contrato_id')
                ->get(),
        ];
    }

    public function getEmpleadosProperty()
    {
        return Empleado::with(['persona', 'departamento', 'cargo', 'tipoContrato', 'sucursal'])
            ->when($this->estadoFilter, fn ($q) => $q->where('estado', $this->estadoFilter))
            ->when($this->departamentoFilter, fn ($q) => $q->where('departamento_id', $this->departamentoFilter))
            ->when($this->tipoContratoFilter, fn ($q) => $q->where('tipo_contrato_id', $this->tipoContratoFilter))
            ->when($this->search, function ($q) {
                $q->whereHas('persona', function ($sq) {
                    $sq->where('nombres', 'like', "%{$this->search}%")
                      ->orWhere('apellidos', 'like', "%{$this->search}%")
                      ->orWhere('numero_documento', 'like', "%{$this->search}%");
                });
            })
            ->orderBy('fecha_ingreso', 'desc')
            ->limit(10)
            ->get();
    }

    public function getDepartamentosListProperty()
    {
        return Departamento::orderBy('nombre')->get(['id', 'nombre']);
    }

    public function getTiposContratoListProperty()
    {
        return TipoContrato::orderBy('nombre')->get(['id', 'nombre']);
    }

    public function render()
    {
        return view('livewire.dashboard.hr-dashboard');
    }
}