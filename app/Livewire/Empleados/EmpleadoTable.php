<?php

namespace App\Livewire\Empleados;

use App\Models\Empleado;
use App\Services\EmpleadoService;
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
                ->sortable()
                ->searchable(),

            Column::make('NOMBRE', 'nombre_completo')
                ->sortable()
                ->searchable(),

            Column::make('DOCUMENTO', 'documento')
                ->sortable()
                ->searchable(),

            Column::make('EMPRESA', 'empresa')
                ->sortable()
                ->searchable(),

            Column::make('SUCURSAL', 'sucursal')
                ->sortable()
                ->searchable(),

            Column::make('DEPARTAMENTO', 'departamento')
                ->sortable()
                ->searchable(),

            Column::make('CARGO', 'cargo')
                ->sortable()
                ->searchable(),

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
            ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>')
            ->class('inline-flex h-7 w-7 items-center justify-center rounded-md text-xs font-medium text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring')
            ->dispatch('edit-empleado', ['empleado' => $row->id])
            ->tooltip('Editar empleado'),

        Button::add('delete')
            ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 11v6"/><path d="M14 11v6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>')
            ->class('inline-flex h-7 w-7 items-center justify-center rounded-md text-xs font-medium text-red-600 transition-colors hover:bg-red-50 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring')
            ->dispatch('open-delete-modal', [
                'component' => 'empleados.empleado-table',
                'method' => 'delete',
                'params' => ['rowId' => $row->id],
                'title' => '¿Eliminar Empleado?',
                'description' => "¿Estás seguro de eliminar a '{$row->nombre_completo}'?",
            ])
            ->tooltip('Eliminar empleado'),
    ];
}

    #[On('delete')]
    public function delete($rowId, EmpleadoService $service): void
    {
        $empleado = Empleado::find($rowId);

        if ($empleado) {
            try {
                $service->deleteEmpleado($empleado);
                $this->dispatch('toast', message: 'Empleado eliminado exitosamente.', type: 'success');
            } catch (\Exception $e) {
                $message = $e instanceof \App\Exceptions\EmpleadoException
                    ? $e->getMessage()
                    : 'Error al eliminar empleado: ' . $e->getMessage();

                $this->dispatch('toast', message: $message, type: 'error');
            }
        }
    }
}
