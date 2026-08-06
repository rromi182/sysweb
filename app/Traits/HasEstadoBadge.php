<?php

namespace App\Traits;

trait HasEstadoBadge
{
    /**
     * Retorna un badge HTML con estilo shadcn según el estado.
     * Usa las clases de color de tu tema.
     */
    public static function estadoBadge(string $estado, array $coloresPersonalizados = []): string
    {
        $colores = array_merge([
            'activo'     => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-400',
            'activa'     => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-400',
            '1'          => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-400',
            'true'       => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-400',
            'inactivo'   => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-400',
            'inactiva'   => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-400',
            '0'          => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-400',
            'false'      => 'bg-red-100 text-red-800 dark:bg-red-900 dark:text-red-400',
            'vacaciones' => 'bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-400',
            'licencia'   => 'bg-yellow-100 text-yellow-800 dark:bg-yellow-900 dark:text-yellow-400',
            'suspendido' => 'bg-orange-100 text-orange-800 dark:bg-orange-900 dark:text-orange-400',
            'pendiente'  => 'bg-amber-100 text-amber-800 dark:bg-amber-900 dark:text-amber-400',
            'completado' => 'bg-emerald-100 text-emerald-800 dark:bg-emerald-900 dark:text-emerald-400',
            'cancelado'  => 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400',
        ], $coloresPersonalizados);

        $clase = $colores[$estado] ?? 'bg-gray-100 text-gray-800 dark:bg-gray-800 dark:text-gray-400';
        $label = ucfirst($estado);

        return "<span class=\"inline-flex items-center rounded-full border border-transparent px-2 py-0.5 text-xs font-medium {$clase} transition-colors\">{$label}</span>";
    }
}