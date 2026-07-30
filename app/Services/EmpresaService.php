<?php

namespace App\Services;

use Exception;
use App\Models\Empresa;
use App\DTOs\EmpresaData;
use Illuminate\Support\Facades\DB;
use App\Exceptions\EmpresaException;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Storage;

class EmpresaService
{
    protected const CACHE_KEY_ALL = 'empresas_list_all';
    protected const CACHE_KEY_ACTIVE = 'empresas_list_active';

    /**
     * Create a new empresa.
     */
    public function createEmpresa(EmpresaData $data): Empresa
    {
        return DB::transaction(function () use ($data) {
            try {
                // Verificar RUC duplicado
                if ($this->existsByRuc($data->ruc)) {
                    throw EmpresaException::duplicateRuc($data->ruc);
                }

                if ($this->existsByNombreEmpresa($data->nombre)) {
                    throw EmpresaException::duplicateName($data->nombre);
                }

                $empresa = Empresa::create($data->toArray());

                $this->clearCache();

                return $empresa;
            } catch (Exception $e) {
                throw EmpresaException::creationFailed($e->getMessage(), [
                    'data' => (array) $data,
                    'trace' => $e->getTraceAsString()
                ]);
            }
        });
    }

    /**
     * Update an existing empresa.
     */
    public function updateEmpresa(Empresa $empresa, EmpresaData $data): Empresa
    {
        return DB::transaction(function () use ($empresa, $data) {
            try {
                // Verificar RUC duplicado (excepto la misma empresa)
                if ($data->ruc && $data->ruc !== $empresa->ruc) {
                    if ($this->existsByRuc($data->ruc)) {
                        throw EmpresaException::duplicateRuc($data->ruc);
                    }
                }

                // Si hay nuevo logo, eliminar el anterior
                if ($data->logo && $empresa->logo) {
                    Storage::disk('public')->delete('empresas/' . $empresa->logo); //verificar ruta
                }

                $empresa->update($data->toArray());

                $this->clearCache();

                return $empresa->refresh();
            } catch (Exception $e) {
                throw EmpresaException::updateFailed($e->getMessage(), [
                    'id'   => $empresa->id,
                    'data' => (array) $data
                ]);
            }
        });
    }

    /**
     * Delete an empresa.
     */
    public function deleteEmpresa(Empresa $empresa): void
    {
        DB::transaction(function () use ($empresa) {
            try {
                // Verificar si tiene relaciones
                if ($empresa->sucursales()->exists()) {
                    throw new Exception("No se puede eliminar la empresa porque tiene sucursales asociadas.");
                }

                if ($empresa->funcionarios()->exists()) {
                    throw new Exception("No se puede eliminar la empresa porque tiene colaboradores asociados.");
                }

                // Eliminar logo si existe
                if ($empresa->logo) {
                    Storage::disk('public')->delete('empresas/' . $empresa->logo);
                }

                $empresa->delete();

                $this->clearCache();
            } catch (Exception $e) {
                throw EmpresaException::deletionFailed($e->getMessage(), ['id' => $empresa->id]);
            }
        });
    }

    /**
     * Get all empresas with cache.
     */
    public function getAllEmpresas(bool $activeOnly = false)
    {
        $cacheKey = $activeOnly ? self::CACHE_KEY_ACTIVE : self::CACHE_KEY_ALL;

        return Cache::remember($cacheKey, 3600, function () use ($activeOnly) {
            $query = Empresa::with(['creador', 'actualizador']);

            if ($activeOnly) {
                $query->activo();
            }

            return $query->orderBy('nombre')->get();
        });
    }

    /**
     * Get empresa by ID with cache.
     */
    public function getEmpresaById(int $id): ?Empresa
    {
        return Cache::remember("empresa_{$id}", 3600, function () use ($id) {
            return Empresa::with(['creador', 'actualizador', 'sucursales'])->find($id);
        });
    }

    /**
     * Check if empresa exists by RUC.
     */
    public function existsByRuc(?string $ruc): bool
    {
        if (!$ruc) {
            return false;
        }

        return Empresa::where('ruc', $ruc)->exists();
    }

    /**
     * Check if empresa exists by RUC.
     */
    public function existsByNombreEmpresa(?string $nombreEmpresa): bool
    {
        if (!$nombreEmpresa) {
            return false;
        }

        return Empresa::where('nombre', $nombreEmpresa)->exists();
    }

    /**
     * Clear all empresa related cache.
     */
    protected function clearCache(): void
    {
        Cache::forget(self::CACHE_KEY_ALL);
        Cache::forget(self::CACHE_KEY_ACTIVE);
    }

    /**
     * Upload empresa logo.
     */
    public function uploadLogo($file, ?string $oldLogo = null): string
    {
        if ($oldLogo) {
            Storage::disk('public')->delete('empresas/' . $oldLogo);
        }

        $path = $file->store('empresas', 'public');
        return basename($path);
    }
}
