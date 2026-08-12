<?php

namespace App\Services;

use App\DTOs\MovimientoNominaDTO;
use App\Exceptions\NominaException;
use App\Models\MovimientoNomina;
use App\Models\LiquidacionSalarial;
use App\Models\ParametroIPS;
use App\Models\Empleado;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class NominaService
{
    /**
     * Tasas IPS Paraguay – Régimen Común
     */
    private const TASA_EMPLEADO = 9.0;      // 9%
    private const TASA_EMPLEADOR = 16.5;    // 16.5%
    private const TASA_TOTAL = 25.5;        // 25.5%

    /**
     * Porcentajes de subsidio según ley paraguaya
     */
    private const SUBSIDIO_ENFERMEDAD = 0.50;   // 50%
    private const SUBSIDIO_ACCIDENTE = 0.75;    // 75%
    private const SUBSIDIO_MATERNIDAD = 1.00;     // 100%

    /**
     * Registrar un movimiento diario/simenausal
     */
    public function registrarMovimiento(MovimientoNominaDTO $dto): MovimientoNomina
    {
        if ($dto->tipo->value === 'otros' && empty($dto->observacion)) {
            throw NominaException::observacionRequerida();
        }

        return MovimientoNomina::create($dto->toArray());
    }

    /**
     * Calcular resumen de ingresos, descuentos e IPS para un empleado en un período
     */
    public function calcularResumen(int $empleadoId, int $anio, int $mes): array
    {
        $movimientos = MovimientoNomina::activos()
            ->porPeriodo($anio, $mes)
            ->where('empleado_id', $empleadoId)
            ->get();

        $ingresos = $movimientos->where('es_ingreso', true)->sum('monto');
        $descuentos = $movimientos->where('es_ingreso', false)->sum('monto');

        $empleado = Empleado::find($empleadoId);
        $salarioBase = $empleado?->salario_base ?? 0;

        $parametroIPS = $this->getParametroIPS($anio, $mes);
        $tasaEmpleado = $parametroIPS?->aporte_empleado ?? self::TASA_EMPLEADO;

        $descuentoIPS = round($salarioBase * ($tasaEmpleado / 100), 2);
        $aportePatronal = round($salarioBase * (self::TASA_EMPLEADOR / 100), 2);

        $neto = $ingresos - $descuentos - $descuentoIPS;

        if ($neto < 0) {
            throw NominaException::salarioNegativo($neto);
        }

        return [
            'empleado_id' => $empleadoId,
            'salario_base' => $salarioBase,
            'total_ingresos' => $ingresos,
            'total_descuentos' => $descuentos,
            'descuento_ips' => $descuentoIPS,
            'aporte_patronal_ips' => $aportePatronal,
            'total_neto' => $neto,
            'detalle' => $movimientos->groupBy(fn($m) => $m->tipo_movimiento->value)
                ->map->sum('monto'),
        ];
    }

    /**
     * Calcular subsidios IPS (enfermedad, accidente, maternidad)
     * Basado en promedio de los últimos 4 meses liquidados
     */
    public function calcularSubsidiosIPS(int $empleadoId, int $anio, int $mes): array
    {
        $ultimasLiquidaciones = $this->getUltimasLiquidaciones($empleadoId, $anio, $mes, 4);

        if ($ultimasLiquidaciones->isEmpty()) {
            $empleado = Empleado::find($empleadoId);
            $promedio4Meses = $empleado?->salario_base ?? 0;
        } else {
            $promedio4Meses = $ultimasLiquidaciones->avg('salario_base') ?? 0;
        }

        $ultimoSalario = $ultimasLiquidaciones->first()?->salario_base
            ?? Empleado::find($empleadoId)?->salario_base
            ?? 0;

        return [
            'promedio_4_meses' => round($promedio4Meses, 2),
            'subsidio_enfermedad_comun' => round($promedio4Meses * self::SUBSIDIO_ENFERMEDAD, 2),
            'subsidio_accidente_laboral' => round($promedio4Meses * self::SUBSIDIO_ACCIDENTE, 2),
            'subsidio_maternidad' => round($ultimoSalario * self::SUBSIDIO_MATERNIDAD, 2),
        ];
    }

    /**
     * Consolidar movimientos en una Liquidación Salarial mensual
     */
    public function consolidarLiquidacion(int $empleadoId, int $anio, int $mes): LiquidacionSalarial
    {
        return DB::transaction(function () use ($empleadoId, $anio, $mes) {
            $resumen = $this->calcularResumen($empleadoId, $anio, $mes);
            $subsidios = $this->calcularSubsidiosIPS($empleadoId, $anio, $mes);
            $parametroIPS = $this->getParametroIPS($anio, $mes);

            return LiquidacionSalarial::updateOrCreate(
                [
                    'empleado_id' => $empleadoId,
                    'periodo_anio' => $anio,
                    'periodo_mes' => $mes,
                    'tipo' => 'ordinaria',
                ],
                [
                    'empresa_id' => Auth::user()?->empresa_id ?? 1,
                    'salario_base' => $resumen['salario_base'],
                    'total_ingresos' => $resumen['total_ingresos'],
                    'total_descuentos' => $resumen['total_descuentos'],
                    'total_aportes_ips' => $resumen['descuento_ips'],
                    'total_neto' => $resumen['total_neto'],
                    'estado' => 'calculado',
                    'fecha_calculo' => now(),
                ]
            );
        });
    }

    /**
     * Verificar si el salario cumple con el mínimo legal vigente
     */
    public function verificarSalarioMinimo(float $salario, int $anio, int $mes): array
    {
        $parametro = $this->getParametroIPS($anio, $mes);
        $salarioMinimo = $parametro?->salario_minimo ?? 0;

        return [
            'salario_actual' => $salario,
            'salario_minimo' => $salarioMinimo,
            'cumple_minimo' => $salario >= $salarioMinimo,
            'diferencia' => round($salarioMinimo - $salario, 2),
            'mensaje' => $salario < $salarioMinimo
                ? "El salario es inferior al mínimo legal ({$salarioMinimo})"
                : "El salario cumple con el mínimo legal",
        ];
    }

    /**
     * Calcular total de aportes patronales de todos los empleados activos
     */
    public function calcularAportesPatronalesTotales(int $anio, int $mes): array
    {
        $parametro = $this->getParametroIPS($anio, $mes);
        $tasa = $parametro?->aporte_empleador ?? self::TASA_EMPLEADOR;

        $empleados = Empleado::where('estado', 'activo')->get();
        $totalAportes = 0;

        foreach ($empleados as $empleado) {
            $totalAportes += $empleado->salario_base * ($tasa / 100);
        }

        return [
            'total_empleados' => $empleados->count(),
            'total_aportes_patronales' => round($totalAportes, 2),
            'tasa_aplicada' => $tasa,
            'periodo' => "{$mes}/{$anio}",
        ];
    }

    /* ------------------------------------------------------------------ */
    /*  Métodos privados                                                  */
    /* ------------------------------------------------------------------ */

    private function getParametroIPS(int $anio, int $mes): ?ParametroIPS
    {
        $empresaId = Auth::user()?->empresa_id ?? 1;

        return ParametroIPS::where('empresa_id', $empresaId)
            ->where('anio', $anio)
            ->where('mes', $mes)
            ->first();
    }

    /**
     * Últimas N liquidaciones ordinarias previas (incluyendo mes actual si existe)
     */
    private function getUltimasLiquidaciones(int $empleadoId, int $anio, int $mes, int $cantidad = 4)
    {
        return LiquidacionSalarial::where('empleado_id', $empleadoId)
            ->where('tipo', 'ordinaria')
            ->where('estado', '!=', 'anulado')
            ->where(function ($query) use ($anio, $mes) {
                $query->where('periodo_anio', '<', $anio)
                    ->orWhere(function ($q) use ($anio, $mes) {
                        $q->where('periodo_anio', $anio)
                          ->where('periodo_mes', '<=', $mes);
                    });
            })
            ->orderBy('periodo_anio', 'desc')
            ->orderBy('periodo_mes', 'desc')
            ->limit($cantidad)
            ->get();
    }
}
