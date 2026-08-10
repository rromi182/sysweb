<?php

namespace App\Services;

use App\DTOs\EmpleadoData;
use App\Exceptions\EmpleadoException;
use App\Models\Empleado;
use App\Models\Persona;
use Exception;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class EmpleadoService
{
    private const CACHE_KEY_ALL = 'empleados_all';
    private const CACHE_KEY_ACTIVE = 'empleados_active';

    /**
     * Crear empleado: persona + empleado en transacción atómica.
     */
    public function createEmpleado(EmpleadoData $data): Empleado
    {
        return DB::transaction(function () use ($data) {
            try {
                if ($this->existsByCode($data->codigo_empleado, $data->empresa_id)) {
                    throw EmpleadoException::duplicateCode($data->codigo_empleado);
                }

                $persona = Persona::create($data->personaArray());

                $empleado = Empleado::create([
                    'persona_id' => $persona->id,
                    ...$data->toArray(),
                ]);

                $this->clearCache();

                Log::info('Empleado creado', [
                    'empleado_id' => $empleado->id,
                    'persona_id' => $persona->id,
                    'codigo' => $empleado->codigo_empleado,
                ]);

                return $empleado->load('persona');

            } catch (Exception $e) {
                if ($e instanceof EmpleadoException) {
                    throw $e;
                }
                throw EmpleadoException::creationFailed($e->getMessage());
            }
        });
    }

    /**
     * Actualizar empleado: persona + empleado en transacción atómica.
     */
    public function updateEmpleado(Empleado $empleado, EmpleadoData $data): Empleado
    {
        return DB::transaction(function () use ($empleado, $data) {
            try {
                if ($data->codigo_empleado !== $empleado->codigo_empleado) {
                    if ($this->existsByCode($data->codigo_empleado, $data->empresa_id, $empleado->id)) {
                        throw EmpleadoException::duplicateCode($data->codigo_empleado);
                    }
                }

                if ($empleado->persona) {
                    $empleado->persona->update($data->personaArray());
                }

                $empleado->update($data->toArray());

                $this->clearCache();

                return $empleado->refresh()->load('persona');

            } catch (Exception $e) {
                if ($e instanceof EmpleadoException) {
                    throw $e;
                }
                throw EmpleadoException::updateFailed($e->getMessage());
            }
        });
    }

    /**
     * Eliminar empleado con validación de dependencias.
     */
    public function deleteEmpleado(Empleado $empleado): void
    {
        DB::transaction(function () use ($empleado) {
            try {
                if ($empleado->asistencias()->exists()) {
                    throw new Exception('No se puede eliminar porque tiene asistencias registradas.');
                }

                if ($empleado->liquidaciones()->exists()) {
                    throw new Exception('No se puede eliminar porque tiene liquidaciones registradas.');
                }

                $empleado->delete();
                $this->clearCache();

            } catch (Exception $e) {
                if ($e instanceof EmpleadoException) {
                    throw $e;
                }
                throw EmpleadoException::deletionFailed($e->getMessage());
            }
        });
    }

    /**
     * Inactivar empleado y su persona asociada.
     */
    public function inactivarEmpleado(Empleado $empleado): void
    {
        DB::transaction(function () use ($empleado) {
            try {
                $empleado->update([
                    'estado' => 'inactivo',
                    'fecha_egreso' => now()->format('Y-m-d'),
                    'actualizado_por' => Auth::id(),
                ]);

                if ($empleado->persona) {
                    $empleado->persona->update([
                        'estado' => 0,
                        'actualizado_por' => Auth::id(),
                    ]);
                }

                $this->clearCache();

            } catch (Exception $e) {
                throw EmpleadoException::updateFailed('Error al inactivar: ' . $e->getMessage());
            }
        });
    }

    /**
     * Generar siguiente código de empleado.
     */
    public function generateNextCode(int $empresaId = 0): string
    {
        $ultimo = Empleado::when($empresaId > 0, fn($q) => $q->where('empresa_id', $empresaId))
            ->orderBy('id', 'desc')
            ->first();

        $siguienteNumero = $ultimo
            ? ((int) preg_replace('/[^0-9]/', '', $ultimo->codigo_empleado) + 1)
            : 1;

        return 'EMP-' . str_pad($siguienteNumero, 3, '0', STR_PAD_LEFT);
    }

    /**
     * Verificar si existe código duplicado.
     */
    public function existsByCode(string $code, int $empresaId, ?int $excludeId = null): bool
    {
        $query = Empleado::where('empresa_id', $empresaId)
            ->where('codigo_empleado', $code);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    /**
     * Listar empleados con cache.
     */
    public function getAllEmpleados(bool $activeOnly = false)
    {
        $cacheKey = $activeOnly ? self::CACHE_KEY_ACTIVE : self::CACHE_KEY_ALL;

        return Cache::remember($cacheKey, 1800, function () use ($activeOnly) {
            $query = Empleado::with(['persona', 'empresa', 'sucursal', 'departamento', 'cargo']);

            if ($activeOnly) {
                $query->activo();
            }

            return $query->orderBy('codigo_empleado')->get();
        });
    }

    /**
     * Buscar empleados para autocomplete / select.
     */
    public function searchEmpleados(?string $query = '', bool $activeOnly = true, int $limit = 50): array
    {
        $empleados = Empleado::query()
            ->with(['persona', 'empresa', 'cargo'])
            ->when($activeOnly, fn($q) => $q->activo())
            ->when($query, function ($q) use ($query) {
                $q->where(function ($sq) use ($query) {
                    $sq->where('empleados.codigo_empleado', 'like', "%{$query}%")
                        ->orWhereHas('persona', function ($pq) use ($query) {
                            $pq->where('nombres', 'like', "%{$query}%")
                                ->orWhere('apellidos', 'like', "%{$query}%")
                                ->orWhere('numero_documento', 'like', "%{$query}%");
                        });
                });
            })
            ->limit($limit)
            ->get()
            ->map(function (Empleado $empleado) {
                return [
                    'value' => $empleado->id,
                    'id' => $empleado->id,
                    'text' => $empleado->nombre_completo,
                    'codigo_empleado' => $empleado->codigo_empleado,
                    'documento' => $empleado->documento,
                    'empresa' => $empleado->empresa?->nombre,
                    'cargo' => $empleado->cargo?->nombre,
                ];
            });

        return $empleados->toArray();
    }

    protected function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_ALL);
        Cache::forget(self::CACHE_KEY_ACTIVE);
    }
}