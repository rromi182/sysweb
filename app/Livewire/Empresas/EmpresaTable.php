<?php

namespace App\Livewire\Empresas;

use App\Models\Empresa;
use Illuminate\Support\Str;
use App\Services\EmpresaService;
use App\Exceptions\EmpresaException;
use App\Traits\HasEstadoBadge;
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
    use HasEstadoBadge;

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
            ->add('estado_badge', fn($model) => self::estadoBadge(
                $model->estado ? 'activo' : 'inactivo'
            ))
            ->add('created_at_formatted', fn(Empresa $model) => $model->created_at?->format('d/m/Y H:i') ?? '-');
    }

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

    public function actions($row): array
    {
        return [
            Button::add('view')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>')
                ->class('inline-flex h-7 w-7 items-center justify-center rounded-md text-xs font-medium text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring')
                ->dispatch('show-empresa', ['empresa' => $row->id])
                ->tooltip('Ver detalles'),

            Button::add('edit')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21.174 6.812a1 1 0 0 0-3.986-3.987L3.842 16.174a2 2 0 0 0-.5.83l-1.321 4.352a.5.5 0 0 0 .623.622l4.353-1.32a2 2 0 0 0 .83-.497z"/><path d="m15 5 4 4"/></svg>')
                ->class('inline-flex h-7 w-7 items-center justify-center rounded-md text-xs font-medium text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring')
                ->dispatch('edit-empresa', ['empresa' => $row->id])
                ->tooltip('Editar empresa'),

            Button::add('delete')
                ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10 11v6"/><path d="M14 11v6"/><path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6"/><path d="M3 6h18"/><path d="M8 6V4a2 2 0 0 1 2-2h4a2 2 0 0 1 2 2v2"/></svg>')
                ->class('inline-flex h-7 w-7 items-center justify-center rounded-md text-xs font-medium text-red-600 transition-colors hover:bg-red-50 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring')
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
