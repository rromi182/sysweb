<div>
    {{-- BARRA DE HERRAMIENTAS MODERNA --}}
    <div class="bg-white rounded-t-xl border-b border-gray-200 px-6 py-4">
        <div class="flex flex-wrap items-center justify-between gap-4">
            {{-- FILTROS --}}
            <div class="flex items-center gap-3">
                <div class="flex items-center gap-2">
                    <label for="anio" class="text-sm font-medium text-gray-700">Año</label>
                    <input id="anio" 
                           wire:model.live="anio" 
                           type="number" 
                           class="w-24 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
                <div class="flex items-center gap-2">
                    <label for="mes" class="text-sm font-medium text-gray-700">Mes</label>
                    <input id="mes" 
                           wire:model.live="mes" 
                           type="number" 
                           min="1" 
                           max="12" 
                           class="w-20 rounded-lg border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 text-sm">
                </div>
            </div>

            {{-- ACCIONES Y TOTAL --}}
            <div class="flex items-center gap-4">
                {{-- BOTONES DE EXPORTACIÓN --}}
                <div class="flex items-center gap-2">
                    <button wire:click="exportarExcel" 
                            class="inline-flex items-center gap-2 px-4 py-2 bg-emerald-600 hover:bg-emerald-700 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                        </svg>
                        Excel
                    </button>
                    
                    <button wire:click="exportarCSV" 
                            class="inline-flex items-center gap-2 px-4 py-2 bg-blue-600 hover:bg-blue-700 text-white text-sm font-medium rounded-lg transition-all duration-200 shadow-sm hover:shadow">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"/>
                        </svg>
                        CSV
                    </button>
                </div>

                {{-- TOTAL GENERAL --}}
                <div class="pl-4 border-l border-gray-300">
                    <div class="text-xs text-gray-500 font-medium uppercase tracking-wider">Total Neto General</div>
                    <div class="text-2xl font-bold text-gray-900">
                        Gs. {{ number_format($totalGeneral, 0, ',', '.') }}
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- TABLA MEJORADA --}}
    <div class="overflow-x-auto">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Colaborador</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Sueldo</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Extra</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Vale</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Ausencia</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Llegada Tardía</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">Otros</th>
                    <th class="px-6 py-3 text-right text-xs font-medium text-gray-700 uppercase tracking-wider bg-gray-100">Total Neto</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-100">
                @forelse($resumen as $empleadoId => $fila)
                    <tr class="hover:bg-indigo-50/50 transition-colors duration-150">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center gap-3">
                                <div class="w-8 h-8 rounded-full bg-indigo-100 flex items-center justify-center text-indigo-700 font-semibold text-xs">
                                    {{ substr($fila['nombre'], 0, 2) }}
                                </div>
                                <span class="text-sm font-medium text-gray-900">{{ $fila['nombre'] }}</span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-700">
                            {{ number_format($fila['sueldo'], 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-emerald-600 font-medium">
                            {{ number_format($fila['extra'], 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-rose-600 font-medium">
                            {{ number_format($fila['vale'], 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-rose-600 font-medium">
                            {{ number_format($fila['ausencia'], 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-rose-600 font-medium">
                            {{ number_format($fila['llegada_tardia'], 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm text-gray-700">
                            {{ number_format($fila['otros'], 0, ',', '.') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-bold text-indigo-700 bg-indigo-50/50">
                            {{ number_format($fila['total_neto'], 0, ',', '.') }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-6 py-12 text-center">
                            <div class="flex flex-col items-center gap-2">
                                <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 17V7m0 10a2 2 0 01-2 2H5a2 2 0 01-2-2V7a2 2 0 012-2h2a2 2 0 012 2m0 10a2 2 0 002 2h2a2 2 0 002-2M9 7a2 2 0 012-2h2a2 2 0 012 2m0 10V7m0 10a2 2 0 002 2h2a2 2 0 002-2V7a2 2 0 00-2-2h-2a2 2 0 00-2 2"/>
                                </svg>
                                <span class="text-sm text-gray-500">No hay movimientos para el período seleccionado</span>
                            </div>
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- PIE DE TABLA --}}
    <div class="bg-gray-50 rounded-b-xl border-t border-gray-200 px-6 py-3">
        <div class="flex items-center justify-between">
            <span class="text-xs text-gray-500">
                Mostrando {{ $resumen->count() }} {{ Str::plural('colaborador', $resumen->count()) }}
            </span>
            <span class="text-xs text-gray-400">
                Actualizado: {{ now()->format('d/m/Y H:i') }}
            </span>
        </div>
    </div>
</div>