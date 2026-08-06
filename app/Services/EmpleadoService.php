<?php

namespace App\Services;

use App\DTOs\EmpleadoData;
use App\Exceptions\EmpleadoException;
use App\Models\Empleado;
use Exception;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\DB;

class EmpleadoService
{
    private const CACHE_KEY_ALL = 'empleados_all';
    private const CACHE_KEY_ACTIVE = 'empleados_active';

    public function createEmpleado(EmpleadoData $data): Empleado
    {
        return DB::transaction(function () use ($data) {
            try {
                if ($this->existsByCode($data->codigo_empleado, $data->empresa_id)) {
                    throw EmpleadoException::duplicateCode($data->codigo_empleado);
                }

                $empleado = Empleado::create($data->toArray());
                $this->clearCache();

                return $empleado;
            } catch (Exception $e) {
                throw EmpleadoException::creationFailed($e->getMessage());
            }
        });
    }

    public function updateEmpleado(Empleado $empleado, EmpleadoData $data): Empleado
    {
        return DB::transaction(function () use ($empleado, $data) {
            try {
                if ($data->codigo_empleado !== $empleado->codigo_empleado) {
                    if ($this->existsByCode($data->codigo_empleado, $data->empresa_id, $empleado->id)) {
                        throw EmpleadoException::duplicateCode($data->codigo_empleado);
                    }
                }

                $empleado->update($data->toArray());
                $this->clearCache();

                return $empleado->refresh();
            } catch (Exception $e) {
                throw EmpleadoException::updateFailed($e->getMessage());
            }
        });
    }

    public function deleteEmpleado(Empleado $empleado): void
    {
        DB::transaction(function () use ($empleado) {
            try {
                if ($empleado->asistencias()->exists()) {
                    throw new Exception("No se puede eliminar porque tiene asistencias registradas.");
                }
                if ($empleado->liquidaciones()->exists()) {
                    throw new Exception("No se puede eliminar porque tiene liquidaciones registradas.");
                }

                $empleado->delete();
                $this->clearCache();
            } catch (Exception $e) {
                throw EmpleadoException::deletionFailed($e->getMessage());
            }
        });
    }

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

    public function existsByCode(string $code, int $empresaId, ?int $excludeId = null): bool
    {
        $query = Empleado::where('empresa_id', $empresaId)
            ->where('codigo_empleado', $code);

        if ($excludeId) {
            $query->where('id', '!=', $excludeId);
        }

        return $query->exists();
    }

    protected function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_ALL);
        Cache::forget(self::CACHE_KEY_ACTIVE);
    }
}