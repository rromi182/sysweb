<?php
// app/Livewire/Nominas/NominaTable.php

namespace App\Livewire\Nominas;

use App\Models\MovimientoNomina;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use Livewire\Attributes\On;
use PowerComponents\LivewirePowerGrid\Button;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Footer;
use PowerComponents\LivewirePowerGrid\Header;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;



class NominaTable extends PowerGridComponent
{
    public string $tableName = 'nomina-table';

    public function setUp(): array
    {
        return [
            PowerGrid::header()
                ->showSearchInput(),
            PowerGrid::footer()
                ->showPerPage()
                ->showRecordCount(),
        ];
    }

    public function datasource(): Builder
    {
        $empresaId = \DB::table('user_empresas')
            ->where('user_id', Auth::id())
            ->value('empresa_id');

        return MovimientoNomina::query()
            ->with(['empleado.persona'])
             ->where('estado', 'activo')          // ← SOLO ACTIVOS
        ->when($empresaId, function ($query) use ($empresaId) {
            return $query->where('movimientos_nomina.empresa_id', $empresaId);
        })
            //BÚSQUEDA PERSONALIZADA
            ->when($this->search, function ($query) {
                $search = $this->search;
                return $query->where(function ($q) use ($search) {
                    // Buscar en columnas de movimientos_nomina
                    $q->where('movimientos_nomina.fecha', 'like', "%{$search}%")
                      ->orWhere('movimientos_nomina.tipo_movimiento', 'like', "%{$search}%")
                      ->orWhere('movimientos_nomina.observacion', 'like', "%{$search}%")
                      // Buscar en la relación empleado → persona
                      ->orWhereHas('empleado.persona', function ($sq) use ($search) {
                          $sq->where('nombres', 'like', "%{$search}%")
                             ->orWhere('apellidos', 'like', "%{$search}%")
                             ->orWhere('numero_documento', 'like', "%{$search}%");
                      });
                });
            });
    }

    public function fields(): PowerGridFields
    {
        return PowerGrid::fields()
            ->add('id')
            ->add('fecha_formatted', fn($model) => $model->fecha->format('d/m/Y'))
            ->add('empleado_nombre', fn($model) => 
                $model->empleado?->persona?->nombres . ' ' . 
                $model->empleado?->persona?->apellidos
            )
            ->add('ci', fn($model) => $model->empleado?->persona?->numero_documento ?? '-')
            ->add('tipo_movimiento')
            ->add('monto_formatted', fn($model) => number_format($model->monto, 0, ',', '.'))
            ->add('es_ingreso_badge', fn($model) => 
                $model->es_ingreso 
                    ? '<span class="px-2 py-1 bg-green-100 text-green-800 rounded-full text-xs">Ingreso</span>'
                    : '<span class="px-2 py-1 bg-red-100 text-red-800 rounded-full text-xs">Descuento</span>'
            )
            ->add('estado_badge', fn($model) =>
                $model->estado === 'anulado'
                    ? '<span class="px-2 py-1 bg-gray-100 text-gray-800 rounded-full text-xs">Anulado</span>'
                    : '<span class="px-2 py-1 bg-blue-100 text-blue-800 rounded-full text-xs">Activo</span>'
            );
    }

    public function columns(): array
    {
        return [
            Column::make('ID', 'id')->hidden(),

            //Busca en la columna REAL 'fecha'
            Column::make('FECHA', 'fecha_formatted')
                ->sortable()
                ->searchable('fecha'),

            //SIN searchable porque es calculada
            Column::make('CI', 'ci')
                ->sortable(),

            // SIN searchable porque es calculada
            Column::make('EMPLEADO', 'empleado_nombre')
                ->sortable(),

            //Busca en la columna REAL 'tipo_movimiento'
            Column::make('TIPO', 'tipo_movimiento')
                ->sortable()
                ->searchable(),

            //SIN searchable porque es calculada (monto_formatted)
            Column::make('MONTO', 'monto_formatted')
                ->sortable(),

            Column::make('NATURALEZA', 'es_ingreso_badge')
                ->sortable(),

            //Busca en la columna REAL 'observacion'
            Column::make('OBS.', 'observacion')
                ->sortable()
                ->searchable(),

            Column::action('ACCIONES'),
        ];
    }

        //BOTONES DE ACCIÓN EN CADA FILA
public function actions(MovimientoNomina $row): array
{
    return [
      /*  Button::add('view')
            ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M2 12s3-7 10-7 10 7 10 7-3 7-10 7-10-7-10-7Z"/><circle cx="12" cy="12" r="3"/></svg>')
            ->class('inline-flex h-7 w-7 items-center justify-center rounded-md text-xs font-medium text-muted-foreground transition-colors hover:bg-accent hover:text-accent-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring')
            ->dispatch('verMovimiento', ['id' => $row->id])
            ->tooltip('Ver detalles del movimiento'),*/

        Button::add('edit')
            ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M17 3a2.85 2.83 0 1 1 4 4L7.5 20.5 2 22l1.5-5.5Z"/></svg>')
            ->class('inline-flex h-7 w-7 items-center justify-center rounded-md text-xs font-medium text-blue-600 transition-colors hover:bg-blue-50 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring')
            ->dispatch('editarMovimiento', ['id' => $row->id])
            ->tooltip('Editar movimiento'),

        Button::add('delete')
            ->slot('<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><circle cx="12" cy="12" r="10"/><path d="m15 9-6 6"/><path d="m9 9 6 6"/></svg>')
            ->class('inline-flex h-7 w-7 items-center justify-center rounded-md text-xs font-medium text-red-600 transition-colors hover:bg-red-50 focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring')
            ->dispatch('open-delete-modal', [
                'component' => 'nominas.nomina-table',
                'method'    => 'delete',
                'params'    => ['rowId' => $row->id],
                'title'     => '¿Eliminar Movimiento?',
                'description' => "¿Estás seguro de que deseas eliminar este movimiento de nómina? Esta acción no se puede deshacer.",
            ])
            ->tooltip('Eliminar movimiento'),
    ];
}

    #[On('delete')]
    public function delete($rowId): void
    {
        $movimiento = MovimientoNomina::find($rowId);

        if ($movimiento) {
            try {
                // Anulación lógica (más seguro para datos de nómina)
                $movimiento->update(['estado' => 'anulado']);
                $this->dispatch('toast', message: 'Movimiento eliminado correctamente.', type: 'success');
            } catch (\Exception $e) {
                $this->dispatch('toast', message: 'Error al anular el movimiento: ' . $e->getMessage(), type: 'error');
            }
        }
    }

    #[On('movimientoGuardado')]
    public function refreshOnSave(): void
    {
        $this->refresh();
    }
}