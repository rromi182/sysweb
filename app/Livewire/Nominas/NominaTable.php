<?php

namespace App\Livewire\Nominas;

use App\Models\MovimientoNomina;
use App\Enums\TipoMovimientoEnum;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class NominaTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'nomina-table';
    public string $sortField = 'fecha';
    public string $sortDirection = 'desc';

    public int $anio;
    public int $mes;
    public ?int $empresa_id = null;

    protected $listeners = [
        'refreshTable' => '$refresh',
        'movimientoGuardado' => '$refresh',
        'actualizar-tabla' => '$refresh',
    ];

    public function mount(): void
    {
        $this->anio = now()->year;
        $this->mes = now()->month;

        $this->cargarEmpresaPorDefecto();
    }

    private function cargarEmpresaPorDefecto(): void
    {
        $user = Auth::user();

        if (!$user) {
            return;
        }

        $this->empresa_id = session('empresa_activa_id');

        if (!$this->empresa_id) {
            try {
                if (method_exists($user, 'empresas')) {
                    $empresa = $user->empresas()->first();
                    if ($empresa) {
                        $this->empresa_id = $empresa->id;
                    }
                }
            } catch (\Exception $e) {
                if (isset($user->empresa_id)) {
                    $this->empresa_id = $user->empresa_id;
                }
            }
        }
    }

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::exportable('export_nomina_' . now()->format('Y_m_d'))
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV)
                ->striped('A6CCD8')
                ->csvSeparator(';')
                ->csvDelimiter('"'),

            PowerGrid::footer()
                ->showPerPage(perPage: 10, perPageValues: [10, 25, 50, 100])
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        $user = Auth::user();

        if (!$user) {
            return MovimientoNomina::query()->whereRaw('1 = 0');
        }

        $empresaIds = [];

        try {
            if (method_exists($user, 'empresas')) {
                $empresaIds = $user->empresas()->pluck('empresas.id')->toArray();
            }
        } catch (\Exception $e) {
            if (isset($user->empresa_id)) {
                $empresaIds = [$user->empresa_id];
            }
        }

        if (empty($empresaIds)) {
            if ($this->empresa_id) {
                $empresaIds = [$this->empresa_id];
            } else {
                return MovimientoNomina::query()->whereRaw('1 = 0');
            }
        }

        $query = MovimientoNomina::query()
            ->with(['empleado.persona', 'empleado.empresa'])
            ->whereIn('empresa_id', $empresaIds)
            ->where('anio', $this->anio)
            ->where('mes', $this->mes);

        if ($this->empresa_id && in_array($this->empresa_id, $empresaIds)) {
            $query->where('empresa_id', $this->empresa_id);
        }

        if (!empty($this->search)) {
            $query->where(function ($q) {
                $q->where('observacion', 'like', "%{$this->search}%")
                    ->orWhereHas('empleado.persona', function ($sq) {
                        $sq->where('nombres', 'like', "%{$this->search}%")
                            ->orWhere('apellidos', 'like', "%{$this->search}%")
                            ->orWhere('numero_documento', 'like', "%{$this->search}%");
                    })
                    ->orWhere('tipo_movimiento', 'like', "%{$this->search}%")
                    ->orWhere('monto', 'like', "%{$this->search}%");
            });
        }

        return $query->orderBy($this->sortField, $this->sortDirection);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('fecha_formatted', fn(MovimientoNomina $model) => $model->fecha->format('d/m/Y'))
            ->add('ci', fn(MovimientoNomina $model) => $model->empleado?->persona?->numero_documento ?? '-')
            ->add('nombre_completo', fn(MovimientoNomina $model) => trim(
                ($model->empleado?->persona?->nombres ?? '') . ' ' .
                    ($model->empleado?->persona?->apellidos ?? '')
            ))
            ->add('anio')
            ->add('mes')
            ->add('tipo_movimiento_label', fn(MovimientoNomina $model) => $this->getTipoLabel($model->tipo_movimiento))
            ->add('tipo_movimiento_badge', fn(MovimientoNomina $model) => $this->getTipoBadge($model->tipo_movimiento))
            ->add('monto_formatted', fn(MovimientoNomina $model) => number_format($model->monto, 0, ',', '.'))
            ->add('monto_with_sign', fn(MovimientoNomina $model) => ($model->es_ingreso ? '+' : '-') . ' ' . number_format($model->monto, 0, ',', '.'))
            ->add('es_ingreso_badge', fn(MovimientoNomina $model) => $model->es_ingreso
                ? '<span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">Ingreso</span>'
                : '<span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">Descuento</span>')
            ->add('observacion', fn(MovimientoNomina $model) => $model->observacion ?? '-')
            ->add('estado_badge', fn(MovimientoNomina $model) => $model->estado === 'activo'
                ? '<span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">Activo</span>'
                : '<span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">Anulado</span>')
            ->add('empresa_nombre', fn(MovimientoNomina $model) => $model->empresa?->nombre ?? '-');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->hidden()
                ->visibleInExport(true),

            Column::make('FECHA', 'fecha_formatted')
                ->sortable()
                ->searchable(),

            Column::make('CI', 'ci')
                ->sortable()
                ->searchable(),

            Column::make('EMPLEADO', 'nombre_completo')
                ->sortable()
                ->searchable(),

            Column::make('EMPRESA', 'empresa_nombre')
                ->sortable()
                ->searchable(),

            Column::make('TIPO', 'tipo_movimiento_badge')
                ->sortable(),

            Column::make('MONTO', 'monto_with_sign')
                ->sortable(),

            Column::make('NATURALEZA', 'es_ingreso_badge')
                ->visibleInExport(false),

            Column::make('ESTADO', 'estado_badge')
                ->visibleInExport(false),

            Column::make('OBSERVACIÓN', 'observacion')
                ->visibleInExport(false),

            Column::action('ACCIONES'),
        ];
    }

    private function getTipoLabel($tipo): string
    {
        $labels = [
            'sueldo' => 'Sueldo',
            'extra' => 'Extra',
            'vale' => 'Vale',
            'ausencia' => 'Ausencia',
            'llegada_tardia' => 'Llegada Tardía',
            'otros' => 'Otros',
        ];

        return $labels[$tipo] ?? $tipo;
    }

    private function getTipoBadge($tipo): string
    {
        $colors = [
            'sueldo' => 'bg-blue-100 text-blue-800',
            'extra' => 'bg-green-100 text-green-800',
            'vale' => 'bg-yellow-100 text-yellow-800',
            'ausencia' => 'bg-red-100 text-red-800',
            'llegada_tardia' => 'bg-orange-100 text-orange-800',
            'otros' => 'bg-gray-100 text-gray-800',
        ];

        $color = $colors[$tipo] ?? 'bg-gray-100 text-gray-800';
        $label = $this->getTipoLabel($tipo);

        return "<span class=\"inline-flex items-center rounded-full px-2 py-0.5 text-xs font-medium {$color}\">{$label}</span>";
    }

    public function actions($row): array
    {
        if ($row->estado === 'anulado') {
            return [];
        }

        return [
            Button::add('editar')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>')
                ->class('inline-flex h-7 w-7 items-center justify-center rounded-md text-xs font-medium text-blue-600 transition-colors hover:bg-blue-50 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring')
                ->dispatch('editarMovimiento', ['id' => $row->id])
                ->tooltip('Editar movimiento'),

            Button::add('anular')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>')
                ->class('inline-flex h-7 w-7 items-center justify-center rounded-md text-xs font-medium text-red-600 transition-colors hover:bg-red-50 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring')
                ->dispatch('anularMovimiento', ['id' => $row->id])
                ->tooltip('Anular movimiento'),
        ];
    }

    #[On('anularMovimiento')]
    public function anularMovimiento(int $id): void
    {
        try {
            $mov = MovimientoNomina::find($id);
            if ($mov) {
                $mov->update(['estado' => 'anulado']);
                $this->dispatch('toast', message: 'Movimiento anulado correctamente', type: 'success');
                $this->dispatch('refreshTable');
            }
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al anular movimiento: ' . $e->getMessage());
            $this->dispatch('toast', message: 'Error al anular: ' . $e->getMessage(), type: 'error');
        }
    }

    #[On('editarMovimiento')]
    public function editarMovimiento(int $id): void
    {
        $this->dispatch('editarMovimientoForm', ['id' => $id]);
    }
}
