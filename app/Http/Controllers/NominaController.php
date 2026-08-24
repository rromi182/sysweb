<?php

namespace App\Http\Controllers;

use App\Services\NominaService;
use App\Http\Requests\NominaMovimientoRequest;
use App\Exceptions\NominaException;
use App\DTOs\MovimientoNominaDTO;
use App\Models\Empleado;
use App\Models\MovimientoNomina;
use App\Models\ParametroIPS;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Barryvdh\DomPDF\Facade\Pdf;


class NominaController extends Controller
{
    public function __construct(
        private NominaService $nominaService
    ) {}

    /**
     * Vista principal: CRUD de movimientos
     */
    public function index()
    {
        return view('nomina.index');
    }

    /**
     * Vista resumen tipo pivot por colaborador
     */
    public function resumen()
    {
        return view('nomina.resumen');
    }

    /**
     * API: Registrar un movimiento de nómina
     */
    public function store(NominaMovimientoRequest $request)
    {
        try {
            // Cambiar auth() por Auth:: para consistencia
            $dto = \App\DTOs\MovimientoNominaDTO::fromRequest(
                $request->validated(),
                Auth::user()->empresa_id,  // ← Cambiado
                Auth::id()                 // ← Cambiado
            );

            $movimiento = $this->nominaService->registrarMovimiento($dto);

            return response()->json([
                'success' => true,
                'message' => 'Movimiento registrado correctamente',
                'data' => $movimiento,
            ], 201);
        } catch (NominaException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 422);
        } catch (\Exception $e) {
            Log::error('Error al registrar movimiento: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno al registrar el movimiento',
            ], 500);
        }
    }

    /**
     * API: Consolidar liquidación mensual
     */
    public function liquidar(Request $request)
    {
        $validated = $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'anio' => 'required|integer|min:2000|max:2100',
            'mes' => 'required|integer|min:1|max:12',
        ]);

        try {
            $liquidacion = $this->nominaService->consolidarLiquidacion(
                $validated['empleado_id'],
                $validated['anio'],
                $validated['mes']
            );

            return response()->json([
                'success' => true,
                'message' => 'Liquidación calculada correctamente',
                'data' => $liquidacion,
            ]);
        } catch (NominaException $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], $e->getCode() ?: 422);
        } catch (\Exception $e) {
            Log::error('Error en liquidación: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error interno al liquidar',
            ], 500);
        }
    }

    /**
     * API: Calcular subsidios IPS
     */
    public function calcularSubsidios(Request $request)
    {
        $validated = $request->validate([
            'empleado_id' => 'required|exists:empleados,id',
            'anio' => 'required|integer|min:2000',
            'mes' => 'required|integer|min:1|max:12',
        ]);

        try {
            $subsidios = $this->nominaService->calcularSubsidiosIPS(
                $validated['empleado_id'],
                $validated['anio'],
                $validated['mes']
            );

            return response()->json([
                'success' => true,
                'data' => $subsidios,
            ]);
        } catch (\Exception $e) {
            Log::error('Error en subsidios: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al calcular subsidios',
            ], 500);
        }
    }

    /**
     * API: Reporte de aportes patronales totales
     */
    public function aportesPatronales(Request $request)
    {
        $validated = $request->validate([
            'anio' => 'required|integer|min:2000',
            'mes' => 'required|integer|min:1|max:12',
        ]);

        try {
            $reporte = $this->nominaService->calcularAportesPatronalesTotales(
                $validated['anio'],
                $validated['mes']
            );

            return response()->json([
                'success' => true,
                'data' => $reporte,
            ]);
        } catch (\Exception $e) {
            Log::error('Error en aportes patronales: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Error al calcular aportes patronales',
            ], 500);
        }
    }

    public function generarRecibo($empleadoId, $anio, $mes)
    {
        $empleado = Empleado::with(['persona', 'empresa', 'cargo'])->findOrFail($empleadoId);

        $movimientos = MovimientoNomina::where('empleado_id', $empleadoId)
            ->where('anio', $anio)->where('mes', $mes)
            ->where('estado', 'activo')->get();

        $salarioBase = $empleado->salario_base;
        $horasExtras = $movimientos->where('tipo_movimiento', 'extra')->sum('monto');
        $totalSalario = $salarioBase + $horasExtras;

        $parametrosIps = ParametroIPS::where('empresa_id', $empleado->empresa_id)
            ->where('anio', $anio)->where('mes', $mes)->first();
        $porcentajeIps = $parametrosIps?->aporte_empleado ?? 9; // 9% default PY
        $descuentoIps = round($totalSalario * $porcentajeIps / 100);

        $adelantos = $movimientos->where('tipo_movimiento', 'vale')->sum('monto');
        $llegadasTardias = $movimientos->where('tipo_movimiento', 'llegada_tardia')->sum('monto');
        $otros = $movimientos->where('tipo_movimiento', 'otros')->where('es_ingreso', 0)->sum('monto');

        $totalDescuentos = $descuentoIps + $adelantos + $llegadasTardias + $otros;
        $saldoCobrar = $totalSalario - $totalDescuentos;

        // Días trabajados desde asistencias
        $diasTrabajados = \DB::table('asistencias')
            ->where('empleado_id', $empleadoId)
            ->whereYear('fecha_laboral', $anio)->whereMonth('fecha_laboral', $mes)
            ->where('estado', 'presente')->count();

        $data = compact(
            'empleado',
            'movimientos',
            'salarioBase',
            'horasExtras',
            'totalSalario',
            'descuentoIps',
            'adelantos',
            'llegadasTardias',
            'otros',
            'totalDescuentos',
            'saldoCobrar',
            'diasTrabajados',
            'anio',
            'mes'
        );

        // Para PDF instalar: composer require barryvdh/laravel-dompdf
       $pdf = Pdf::loadView('nomina.recibo', $data);
        return $pdf->stream("recibo_{$empleadoId}_{$anio}_{$mes}.pdf");
    }
}
