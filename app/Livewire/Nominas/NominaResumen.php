<?php

namespace App\Livewire\Nominas;

use Livewire\Component;
use App\Models\MovimientoNomina;
use App\Enums\TipoMovimientoEnum;
use Illuminate\Support\Facades\Auth;

class NominaResumen extends Component
{
    public int $anio;
    public int $mes;

    public function mount()
    {
        $this->anio = now()->year;
        $this->mes = now()->month;
    }

    public function getResumenProperty()
    {
        return MovimientoNomina::activos()
            ->porPeriodo($this->anio, $this->mes)
            ->where('empresa_id', Auth::user()->empresa_id)
            ->with('empleado.persona')
            ->get()
            ->groupBy('empleado_id')
            ->map(function ($movimientos) {
                $empleado = $movimientos->first()->empleado;
                $nombre = trim(($empleado->persona->nombres ?? '') . ' ' . ($empleado->persona->apellidos ?? ''));

                $sueldo = $movimientos->where('tipo_movimiento', TipoMovimientoEnum::SUELDO)->sum('monto');
                $extra = $movimientos->where('tipo_movimiento', TipoMovimientoEnum::EXTRA)->sum('monto');
                $vale = $movimientos->where('tipo_movimiento', TipoMovimientoEnum::VALE)->sum('monto');
                $ausencia = $movimientos->where('tipo_movimiento', TipoMovimientoEnum::AUSENCIA)->sum('monto');
                $tardia = $movimientos->where('tipo_movimiento', TipoMovimientoEnum::LLEGADA_TARDIA)->sum('monto');
                $otros = $movimientos->where('tipo_movimiento', TipoMovimientoEnum::OTROS)->sum('monto');

                $ingresos = $movimientos->where('es_ingreso', true)->sum('monto');
                $descuentos = $movimientos->where('es_ingreso', false)->sum('monto');

                return [
                    'nombre' => $nombre,
                    'sueldo' => $sueldo,
                    'extra' => $extra,
                    'vale' => $vale,
                    'ausencia' => $ausencia,
                    'llegada_tardia' => $tardia,
                    'otros' => $otros,
                    'total_ingresos' => $ingresos,
                    'total_descuentos' => $descuentos,
                    'total_neto' => $ingresos - $descuentos,
                ];
            })
            ->sortByDesc('total_neto');
    }

    public function getTotalGeneralProperty()
    {
        return $this->resumen->sum('total_neto');
    }

    public function render()
{
    return view('livewire.nominas.nomina-resumen');
}
}
