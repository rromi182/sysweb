<?php
// app/Livewire/Nominas/NominaResumen.php

namespace App\Livewire\Nominas;

use Livewire\Component;
use App\Models\MovimientoNomina;
use App\Enums\TipoMovimientoEnum;
use Illuminate\Support\Facades\Auth;
use PowerComponents\LivewirePowerGrid\Traits\WithExport;
use PowerComponents\LivewirePowerGrid\Components\SetUp\Exportable;

class NominaResumen extends Component
{
    use WithExport;

    public int $anio;
    public int $mes;

    public function mount()
    {
        $this->anio = now()->year;
        $this->mes = now()->month;
    }

    public function getResumenProperty()
    {
        $empresaId = \DB::table('user_empresas')
            ->where('user_id', Auth::id())
            ->value('empresa_id');

        if (!$empresaId) {
            return collect();
        }

        return MovimientoNomina::activos()
            ->porPeriodo($this->anio, $this->mes)
            ->where('empresa_id', $empresaId)
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

    // 🔥 EXPORTAR A EXCEL
    public function exportarExcel()
    {
        $resumen = $this->resumen;

        if ($resumen->isEmpty()) {
            session()->flash('error', 'No hay datos para exportar');
            return;
        }

        $nombreArchivo = 'resumen_nomina_' . $this->anio . '_' . str_pad($this->mes, 2, '0', STR_PAD_LEFT) . '.xls';

        $html = $this->generarHTMLExport($resumen);

        return response($html, 200, [
            'Content-Type' => 'application/vnd.ms-excel; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"',
        ]);
    }

    // 🔥 EXPORTAR A CSV
    public function exportarCSV()
    {
        $resumen = $this->resumen;

        if ($resumen->isEmpty()) {
            session()->flash('error', 'No hay datos para exportar');
            return;
        }

        $nombreArchivo = 'resumen_nomina_' . $this->anio . '_' . str_pad($this->mes, 2, '0', STR_PAD_LEFT) . '.csv';

        return response()->stream(function () use ($resumen) {
            $handle = fopen('php://output', 'w');
            fprintf($handle, chr(0xEF) . chr(0xBB) . chr(0xBF));

            fputcsv($handle, [
                'Nombre y Apellido',
                'Sueldo',
                'Extra',
                'Vale',
                'Ausencia',
                'Llegada Tardía',
                'Otros',
                'Total Ingresos',
                'Total Descuentos',
                'Total Neto'
            ], ';');

            foreach ($resumen as $fila) {
                fputcsv($handle, [
                    $fila['nombre'],
                    number_format($fila['sueldo'], 0, ',', '.'),
                    number_format($fila['extra'], 0, ',', '.'),
                    number_format($fila['vale'], 0, ',', '.'),
                    number_format($fila['ausencia'], 0, ',', '.'),
                    number_format($fila['llegada_tardia'], 0, ',', '.'),
                    number_format($fila['otros'], 0, ',', '.'),
                    number_format($fila['total_ingresos'], 0, ',', '.'),
                    number_format($fila['total_descuentos'], 0, ',', '.'),
                    number_format($fila['total_neto'], 0, ',', '.'),
                ], ';');
            }

            fputcsv($handle, [
                'TOTAL GENERAL',
                '', '', '', '', '', '', '', '',
                number_format($this->totalGeneral, 0, ',', '.')
            ], ';');

            fclose($handle);
        }, 200, [
            'Content-Type' => 'text/csv; charset=UTF-8',
            'Content-Disposition' => 'attachment; filename="' . $nombreArchivo . '"',
        ]);
    }

    private function generarHTMLExport($resumen)
    {
        $html = '<html xmlns:o="urn:schemas-microsoft-com:office:office" 
                      xmlns:x="urn:schemas-microsoft-com:office:excel" 
                      xmlns="http://www.w3.org/TR/REC-html40">
        <head>
            <meta charset="UTF-8">
            <!--[if gte mso 9]>
            <xml>
                <x:ExcelWorkbook>
                    <x:ExcelWorksheets>
                        <x:ExcelWorksheet>
                            <x:Name>Resumen Nómina</x:Name>
                            <x:WorksheetOptions>
                                <x:DisplayGridlines/>
                            </x:WorksheetOptions>
                        </x:ExcelWorksheet>
                    </x:ExcelWorksheets>
                </x:ExcelWorkbook>
            </xml>
            <![endif]-->
            <style>
                th { background-color: #4F81BD; color: #FFFFFF; font-weight: bold; padding: 5px; }
                td { padding: 5px; border: 1px solid #999; }
                .total { background-color: #E6E6E6; font-weight: bold; }
                .text-right { text-align: right; }
            </style>
        </head>
        <body>
            <table>
                <thead>
                    <tr>
                        <th>Nombre y Apellido</th>
                        <th>Sueldo</th>
                        <th>Extra</th>
                        <th>Vale</th>
                        <th>Ausencia</th>
                        <th>Llegada Tardía</th>
                        <th>Otros</th>
                        <th>Total Ingresos</th>
                        <th>Total Descuentos</th>
                        <th>Total Neto</th>
                    </tr>
                </thead>
                <tbody>';

        foreach ($resumen as $fila) {
            $html .= '<tr>
                        <td>' . $fila['nombre'] . '</td>
                        <td class="text-right">' . number_format($fila['sueldo'], 0, ',', '.') . '</td>
                        <td class="text-right">' . number_format($fila['extra'], 0, ',', '.') . '</td>
                        <td class="text-right">' . number_format($fila['vale'], 0, ',', '.') . '</td>
                        <td class="text-right">' . number_format($fila['ausencia'], 0, ',', '.') . '</td>
                        <td class="text-right">' . number_format($fila['llegada_tardia'], 0, ',', '.') . '</td>
                        <td class="text-right">' . number_format($fila['otros'], 0, ',', '.') . '</td>
                        <td class="text-right">' . number_format($fila['total_ingresos'], 0, ',', '.') . '</td>
                        <td class="text-right">' . number_format($fila['total_descuentos'], 0, ',', '.') . '</td>
                        <td class="text-right">' . number_format($fila['total_neto'], 0, ',', '.') . '</td>
                    </tr>';
        }

        $html .= '<tr class="total">
                    <td><strong>TOTAL GENERAL</strong></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td class="text-right"><strong>' . number_format($this->totalGeneral, 0, ',', '.') . '</strong></td>
                </tr>
                </tbody>
            </table>
        </body>
        </html>';

        return $html;
    }

    public function render()
    {
        // 🔥 PASAR AMBAS VARIABLES EXPLÍCITAMENTE
        return view('livewire.nominas.nomina-resumen', [
            'resumen' => $this->resumen,
            'totalGeneral' => $this->totalGeneral,
        ]);
    }
}