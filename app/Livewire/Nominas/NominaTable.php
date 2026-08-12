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

    protected $listeners = [
        'refreshTable' => '$refresh',
        'movimientoGuardado' => '$refresh',
        'actualizar-tabla' => '$refresh',
    ];

    public function mount(): void
    {
        $this->anio = now()->year;
        $this->mes = now()->month;
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

            PowerGrid::header(),

            PowerGrid::footer()
                ->showPerPage(perPage: 10, perPageValues: [10, 25, 50, 100])
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return MovimientoNomina::query()
            ->with(['empleado.persona'])
            ->where('empresa_id', Auth::user()->empresa_id)
            ->where('anio', $this->anio)
            ->where('mes', $this->mes)
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('observacion', 'like', "%{$search}%")
                        ->orWhereHas('empleado.persona', function ($sq) use ($search) {
                            $sq->where('nombres', 'like', "%{$search}%")
                                ->orWhere('apellidos', 'like', "%{$search}%")
                                ->orWhere('numero_documento', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('fecha_formatted', fn(MovimientoNomina $model) => $model->fecha->format('d/m/Y'))
            ->add('ci', fn(MovimientoNomina $model) => $model->empleado->persona->numero_documento ?? '-')
            ->add('nombre_completo', fn(MovimientoNomina $model) => trim(($model->empleado->persona->nombres ?? '') . ' ' . ($model->empleado->persona->apellidos ?? '')))
            ->add('anio')
            ->add('mes')
            ->add('tipo_movimiento_label', fn(MovimientoNomina $model) => $model->tipo_movimiento->label())
            ->add('monto_formatted', fn(MovimientoNomina $model) => number_format($model->monto, 0, ',', '.'))
            ->add('es_ingreso_badge', fn(MovimientoNomina $model) => $model->es_ingreso
                ? '<span class="inline-flex items-center rounded-full bg-green-100 px-2 py-0.5 text-xs font-medium text-green-800">Ingreso</span>'
                : '<span class="inline-flex items-center rounded-full bg-red-100 px-2 py-0.5 text-xs font-medium text-red-800">Descuento</span>')
            ->add('observacion', fn(MovimientoNomina $model) => $model->observacion ?? '-');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->hidden()
                ->visibleInExport(true),

            Column::make('FECHA', 'fecha_formatted')
                ->sortable(),

            Column::make('CI', 'ci')
                ->sortable(),

            Column::make('NOMBRE Y APELLIDO', 'nombre_completo')
                ->sortable(),

            Column::make('AÑO', 'anio')
                ->sortable(),

            Column::make('MES', 'mes')
                ->sortable(),

            Column::make('TIPO MOVIMIENTO', 'tipo_movimiento_label')
                ->sortable(),

            Column::make('MONTO', 'monto_formatted')
                ->sortable(),

            Column::make('NATURALEZA', 'es_ingreso_badge')
                ->visibleInExport(false),

            Column::make('OBSERVACIÓN', 'observacion')
                ->visibleInExport(false),

            Column::action('ACCIONES'),
        ];
    }

    public function actions($row): array
    {
        return [
            Button::add('editar')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>')
                ->class('inline-flex h-7 w-7 items-center justify-center rounded-md text-xs font-medium text-blue-600 transition-colors hover:bg-blue-50 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring')
                ->dispatch('editarMovimientoForm', ['id' => $row->id])
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
}