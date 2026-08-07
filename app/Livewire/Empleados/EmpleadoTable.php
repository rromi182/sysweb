<?php

namespace App\Livewire\Empleados;

use App\Models\Empleado;
use App\Services\EmpleadoService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Blade;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;
use App\Traits\HasEstadoBadge;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;

final class EmpleadoTable extends PowerGridComponent
{
    use WithExport;
    use HasEstadoBadge;

    public string $tableName = 'empleado-table';
    public string $sortField = 'codigo_empleado';
    public string $sortDirection = 'asc';

    protected $listeners = [
        'refreshTable' => '$refresh',
        'empleadoCreated' => '$refresh',
        'empleadoUpdated' => '$refresh',
    ];

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::exportable('export_' . now()->format('Y_m_d'))
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV)
                ->striped('A6CCD8') // opcional: color de stripe para export
                ->csvSeparator(';')
                ->csvDelimiter('"'),

            // NO usar showSearchInput() aquí — lo ponemos en el header custom
            PowerGrid::header()
                ->includeViewOnTop('components.table-toolbar'), // header personalizado

            PowerGrid::footer()
                ->showPerPage(perPage: 10, perPageValues: [10, 25, 50, 100])
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Empleado::query()
            ->with(['persona', 'empresa', 'sucursal', 'departamento', 'cargo'])
            ->when($this->search, function ($query, $search) {
                $query->where(function ($q) use ($search) {
                    $q->where('empleados.codigo_empleado', 'like', "%{$search}%")
                        ->orWhereHas('persona', function ($sq) use ($search) {
                            $sq->where('nombres', 'like', "%{$search}%")
                                ->orWhere('apellidos', 'like', "%{$search}%")
                                ->orWhere('numero_documento', 'like', "%{$search}%");
                        })
                        ->orWhereHas('empresa', function ($sq) use ($search) {
                            $sq->where('nombre', 'like', "%{$search}%");
                        })
                        ->orWhereHas('sucursal', function ($sq) use ($search) {
                            $sq->where('nombre', 'like', "%{$search}%");
                        })
                        ->orWhereHas('departamento', function ($sq) use ($search) {
                            $sq->where('nombre', 'like', "%{$search}%");
                        })
                        ->orWhereHas('cargo', function ($sq) use ($search) {
                            $sq->where('nombre', 'like', "%{$search}%");
                        });
                });
            })
            ->orderBy($this->sortField, $this->sortDirection);
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('codigo_empleado')
            ->add('nombre_completo', fn(Empleado $model) => $model->nombre_completo)
            ->add('documento', fn(Empleado $model) => $model->documento)
            ->add('empresa', fn(Empleado $model) => $model->empresa?->nombre ?? 'N/A')
            ->add('sucursal', fn(Empleado $model) => $model->sucursal?->nombre ?? 'N/A')
            ->add('departamento', fn(Empleado $model) => $model->departamento?->nombre ?? 'N/A')
            ->add('cargo', fn(Empleado $model) => $model->cargo?->nombre ?? 'N/A')
            ->add('salario_base_formatted', fn(Empleado $model) => format_money($model->salario_base))
            ->add('estado_badge', fn(Empleado $model) => self::estadoBadge($model->estado))
            ->add('fecha_ingreso_formatted', fn(Empleado $model) => $model->fecha_ingreso?->format('d/m/Y') ?? '-');
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->hidden()
                ->visibleInExport(true),

            Column::make('CÓDIGO', 'codigo_empleado')
                ->sortable(),

            Column::make('NOMBRE', 'nombre_completo')
                ->sortable(),

            Column::make('DOCUMENTO', 'documento')
                ->sortable(),

            Column::make('EMPRESA', 'empresa')
                ->sortable(),

            Column::make('SUCURSAL', 'sucursal')
                ->sortable(),

            Column::make('DEPARTAMENTO', 'departamento')
                ->sortable(),

            Column::make('CARGO', 'cargo')
                ->sortable(),

            Column::make('SALARIO', 'salario_base_formatted')
                ->visibleInExport(false),

            Column::make('ESTADO', 'estado_badge')
                ->visibleInExport(false),

            Column::make('INGRESO', 'fecha_ingreso_formatted')
                ->sortable()
                ->visibleInExport(false),

            Column::action('ACCIONES'),
        ];
    }

    public function actions($row): array
    {
        return [
            Button::add('view')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>')
                ->class('inline-flex h-7 w-7 items-center justify-center rounded-md text-xs font-medium text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring')
                ->dispatch('show-empleado', ['empleado' => $row->id])
                ->tooltip('Ver detalles'),

            Button::add('edit')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>')
                ->class('inline-flex h-7 w-7 items-center justify-center rounded-md text-xs font-medium text-blue-600 transition-colors hover:bg-blue-50 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring')
                ->dispatch('edit-empleado', ['empleado' => $row->id])
                ->tooltip('Editar empleado'),

            Button::add('inactivar')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>')
                ->class('inline-flex h-7 w-7 items-center justify-center rounded-md text-xs font-medium text-orange-600 transition-colors hover:bg-orange-50 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring')
                ->dispatch('open-delete-modal', [
                    'component' => 'empleados.empleado-table',
                    'method' => 'inactivar',
                    'params' => ['rowId' => $row->id],
                    'title' => '¿Inactivar Empleado?',
                    'description' => "¿Estás seguro de inactivar a '{$row->nombre_completo}'? Este cambio puede revertirse editando el empleado.",
                ])
                ->tooltip('Inactivar empleado'),
        ];
    }

    #[On('inactivar')]
    public function inactivar(int $rowId): void
    {
        try {
            $empleado = Empleado::find($rowId);

            if (!$empleado) {
                $this->dispatch('toast', message: 'Empleado no encontrado', type: 'error');
                return;
            }

            // Inactivar empleado
            $empleado->update([
                'estado' => 'inactivo',
                'fecha_egreso' => now()->format('Y-m-d'),
                'actualizado_por' => Auth::id(),
            ]);

            // Inactivar persona asociada (estado 0 = inactivo)
            if ($empleado->persona) {
                $empleado->persona->update([
                    'estado' => 0,
                    'actualizado_por' => Auth::id(),
                ]);
            }

            $this->dispatch('toast', message: 'Empleado inactivado correctamente', type: 'success');
        } catch (\Exception $e) {
            \Illuminate\Support\Facades\Log::error('Error al inactivar empleado: ' . $e->getMessage());
            $this->dispatch('toast', message: 'Error: ' . $e->getMessage(), type: 'error');
        }
    }
}
