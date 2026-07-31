<?php

namespace App\Livewire\Empresas;

use App\Models\Empresa;
use Illuminate\Support\Str;
use App\Services\EmpresaService;
use App\Exceptions\EmpresaException;
use Illuminate\Database\Eloquent\Builder;
use PowerComponents\LivewirePowerGrid\Button;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;

final class EmpresaTable extends PowerGridComponent
{
    use WithExport;

    public string $tableName = 'empresa-table';
    public string $sortField = 'created_at';
    public string $sortDirection = 'desc';

    // Event listeners
    protected $listeners = [
        'refreshTable' => '$refresh',
        'empresaCreated' => '$refresh',
        'empresaUpdated' => '$refresh',
    ];

    public function setUp(): array
    {
        $this->showCheckBox();

        return [
            PowerGrid::exportable('empresa_export_' . now()->format('Y_m_d'))
                ->type(Exportable::TYPE_XLS, Exportable::TYPE_CSV),

            PowerGrid::header()
                ->showSearchInput()
                ->includeViewOnTop('livewire.empresas.empresa-table-header'),

            PowerGrid::footer()
                ->showPerPage(perPage: 10, perPageValues: [10, 25, 50, 100])
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        return Empresa::query()
            //->with(['creador', 'actualizador'])
            ->orderBy($this->sortField, $this->sortDirection);
    }

  /*  public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('nombre')
            ->add('razon_social')
            ->add('ruc')
            ->add('direccion', fn(Empresa $model) => Str::limit($model->direccion, 30))
            ->add('telefono')
            ->add('correo')
            ->add('logo', fn(Empresa $model) => $model->logo ? asset('storage/empresas/' . $model->logo) : null)
            ->add('sitio_web')
            ->add('estado', fn(Empresa $model) => $model->estado ? 'Activo' : 'Inactivo')
            ->add('estado_badge', fn(Empresa $model) => $model->estado
                ? '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Activo</span>'
                : '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Inactivo</span>')
            // CORREGIR: Usar formato ISO para que Carbon lo entienda
            ->add('created_at_formatted', fn(Empresa $model) => $model->created_at?->format('d/m/Y H:i') ?? '-')
            ->add('updated_at_formatted', fn(Empresa $model) => $model->updated_at?->format('d/m/Y H:i') ?? '-');
    }*/

    public function fields(): PowerGridFields
{
    return PowerGrid::fields()
        ->add('id')
        ->add('nombre')
        ->add('razon_social')
        ->add('ruc')
        ->add('direccion', fn($model) => Str::limit($model->direccion, 30))
        ->add('telefono')
        ->add('correo')
        ->add('estado_badge', function($model) {
            return $model->estado 
                ? '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800 dark:bg-green-900 dark:text-green-300">
                     <span class="w-1.5 h-1.5 mr-1 rounded-full bg-green-400"></span>
                     Activo
                   </span>'
                : '<span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-300">
                     <span class="w-1.5 h-1.5 mr-1 rounded-full bg-red-400"></span>
                     Inactivo
                   </span>';
        })
        ->add('created_at_formatted', fn(Empresa $model) => $model->created_at?->format('d/m/Y H:i') ?? '-');
}

    /*public function columns(): array
    {
        return [
            Column::make('ID', 'id')
                ->hidden()
                ->visibleInExport(true),

            Column::make('Nombre', 'nombre')
                ->sortable()
                ->searchable(),

            Column::make('Razón Social', 'razon_social')
                ->sortable()
                ->searchable(),

            Column::make('RUC', 'ruc')
                ->sortable()
                ->searchable(),

            Column::make('Dirección', 'direccion')
                ->sortable()
                ->searchable(),

            Column::make('Teléfono', 'telefono')
                ->sortable()
                ->searchable(),

            Column::make('Correo', 'correo')
                ->sortable()
                ->searchable(),

            Column::make('Estado', 'estado_badge')
                ->sortable()
                ->searchable(),

            // CORREGIR: Usar el campo formateado
            Column::make('Creado', 'created_at_formatted')
                ->field('created_at') // Para ordenar por la fecha original
                ->sortable()
                ->hidden(),

            Column::action('Acciones'),
        ];
    }*/
        public function columns(): array
{
    return [
        Column::make('ID', 'id')->hidden(),
        
        Column::make('NOMBRE', 'nombre')
            ->sortable()
            ->searchable(),
            
        Column::make('RAZÓN SOCIAL', 'razon_social')
            ->sortable()
            ->searchable(),
            
        Column::make('RUC', 'ruc')
            ->sortable()
            ->searchable(),
            
        Column::make('DIRECCIÓN', 'direccion')
            ->sortable()
            ->searchable(),
            
        Column::make('TELÉFONO', 'telefono')
            ->sortable()
            ->searchable(),
            
        Column::make('CORREO', 'correo')
            ->sortable()
            ->searchable(),
            
        Column::make('ESTADO', 'estado_badge'),
        
        Column::action('ACCIONES'),
    ];
}

  /*  public function actions(Empresa $row): array
    {
        return [
            Button::add('view')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" /><path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" /></svg>')
                ->class('bg-blue-500 hover:bg-blue-600 text-white p-2 rounded-md flex items-center justify-center w-8 h-8')
                ->dispatch('show-empresa', ['empresa' => $row->id])
                ->tooltip('Ver empresa'),

            Button::add('edit')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" /></svg>')
                ->class('bg-amber-500 hover:bg-amber-600 text-white p-2 rounded-md flex items-center justify-center w-8 h-8')
                ->dispatch('edit-empresa', ['empresa' => $row->id])
                ->tooltip('Editar empresa'),

            Button::add('delete')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4"><path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" /></svg>')
                ->class('bg-red-500 hover:bg-red-600 text-white p-2 rounded-md flex items-center justify-center w-8 h-8')
                ->dispatch('open-delete-modal', [
                    'component' => 'empresas.empresa-table',
                    'method' => 'delete',
                    'params' => ['rowId' => $row->id],
                    'title' => '¿Eliminar Empresa?',
                    'description' => "¿Estás seguro de eliminar la empresa '{$row->nombre}'? Esta acción no se puede deshacer.",
                ])
                ->tooltip('Eliminar empresa'),
        ];
    }*/

        public function actions($row): array
{
    return [
        Button::add('view')
            ->slot('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="M2.036 12.322a1.012 1.012 0 0 1 0-.639C3.423 7.51 7.36 4.5 12 4.5c4.638 0 8.573 3.007 9.963 7.178.07.207.07.431 0 .639C20.577 16.49 16.64 19.5 12 19.5c-4.638 0-8.573-3.007-9.963-7.178Z" />
                <path stroke-linecap="round" stroke-linejoin="round" d="M15 12a3 3 0 1 1-6 0 3 3 0 0 1 6 0Z" />
            </svg>')
            ->class('inline-flex items-center justify-center w-8 h-8 text-blue-600 bg-blue-100 hover:bg-blue-200 rounded-md transition-colors duration-200')
            ->dispatch('show-empresa', ['empresa' => $row->id])
            ->tooltip('Ver detalles'),

        Button::add('edit')
            ->slot('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="m16.862 4.487 1.687-1.688a1.875 1.875 0 1 1 2.652 2.652L10.582 16.07a4.5 4.5 0 0 1-1.897 1.13L6 18l.8-2.685a4.5 4.5 0 0 1 1.13-1.897l8.932-8.931Zm0 0L19.5 7.125M18 14v4.75A2.25 2.25 0 0 1 15.75 21H5.25A2.25 2.25 0 0 1 3 18.75V8.25A2.25 2.25 0 0 1 5.25 6H10" />
            </svg>')
            ->class('inline-flex items-center justify-center w-8 h-8 text-amber-600 bg-amber-100 hover:bg-amber-200 rounded-md transition-colors duration-200')
            ->dispatch('edit-empresa', ['empresa' => $row->id])
            ->tooltip('Editar empresa'),

        Button::add('delete')
            ->slot('<svg xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="w-4 h-4">
                <path stroke-linecap="round" stroke-linejoin="round" d="m14.74 9-.346 9m-4.788 0L9.26 9m9.968-3.21c.342.052.682.107 1.022.166m-1.022-.165L18.16 19.673a2.25 2.25 0 0 1-2.244 2.077H8.084a2.25 2.25 0 0 1-2.244-2.077L4.772 5.79m14.456 0a48.108 48.108 0 0 0-3.478-.397m-12 .562c.34-.059.68-.114 1.022-.165m0 0a48.11 48.11 0 0 1 3.478-.397m7.5 0v-.916c0-1.18-.91-2.164-2.09-2.201a51.964 51.964 0 0 0-3.32 0c-1.18.037-2.09 1.022-2.09 2.201v.916m7.5 0a48.667 48.667 0 0 0-7.5 0" />
            </svg>')
            ->class('inline-flex items-center justify-center w-8 h-8 text-red-600 bg-red-100 hover:bg-red-200 rounded-md transition-colors duration-200')
            ->dispatch('open-delete-modal', [
                'component' => 'empresas.empresa-table',
                'method' => 'delete',
                'params' => ['rowId' => $row->id],
                'title' => '¿Eliminar Empresa?',
                'description' => "¿Estás seguro de eliminar '{$row->nombre}'?",
            ])
            ->tooltip('Eliminar empresa'),
    ];
}

    #[On('delete')]
    public function delete($rowId, EmpresaService $service): void
    {
        $empresa = Empresa::find($rowId);

        if ($empresa) {
            try {
                $service->deleteEmpresa($empresa);
                $this->dispatch('toast', message: 'Empresa eliminada exitosamente.', type: 'success');
                $this->dispatch('refreshTable');
            } catch (\Exception $e) {
                $message = $e instanceof EmpresaException
                    ? $e->getMessage()
                    : 'Error al eliminar empresa: ' . $e->getMessage();

                $this->dispatch('toast', message: $message, type: 'error');
            }
        }
    }
}
