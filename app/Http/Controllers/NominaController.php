<?php

namespace App\Http\Controllers;

use App\Services\NominaService;
use App\Http\Requests\NominaMovimientoRequest;
use App\Exceptions\NominaException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;  
use Illuminate\Support\Facades\Log;   

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
}