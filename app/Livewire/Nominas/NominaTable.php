<?php
// app/Livewire/Nominas/NominaTable.php

namespace App\Livewire\Nominas;

use App\Models\MovimientoNomina;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Facades\Auth;
use PowerComponents\LivewirePowerGrid\Column;
use PowerComponents\LivewirePowerGrid\Facades\PowerGrid;
use PowerComponents\LivewirePowerGrid\PowerGridComponent;
use PowerComponents\LivewirePowerGrid\PowerGridFields;

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
            ->when($empresaId, function ($query) use ($empresaId) {
                return $query->where('movimientos_nomina.empresa_id', $empresaId);
            })
            // 🔥 BÚSQUEDA PERSONALIZADA
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

            // ✅ Busca en la columna REAL 'fecha'
            Column::make('Fecha', 'fecha_formatted')
                ->sortable()
                ->searchable('fecha'),

            // ❌ SIN searchable porque es calculada
            Column::make('CI', 'ci')
                ->sortable(),

            // ❌ SIN searchable porque es calculada
            Column::make('Empleado', 'empleado_nombre')
                ->sortable(),

            // ✅ Busca en la columna REAL 'tipo_movimiento'
            Column::make('Tipo', 'tipo_movimiento')
                ->sortable()
                ->searchable(),

            // ❌ SIN searchable porque es calculada (monto_formatted)
            Column::make('Monto', 'monto_formatted')
                ->sortable(),

            Column::make('Naturaleza', 'es_ingreso_badge')
                ->sortable(),

            Column::make('Estado', 'estado_badge')
                ->sortable(),

            // ✅ Busca en la columna REAL 'observacion'
            Column::make('Observación', 'observacion')
                ->sortable()
                ->searchable(),
        ];
    }
}