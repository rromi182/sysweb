<div>
    <x-modal name="nomina-form-modal" :title="''" maxWidth="2xl">
        <div class="p-6">
            {{-- Header --}}
            <div class="mb-4 pb-3 border-b border-border">
                <h3 class="text-lg font-semibold text-foreground">
                    {{ $modoEdicion ? 'Editar Movimiento' : 'Nuevo Movimiento' }}
                </h3>
            </div>

            <form wire:submit="save" class="space-y-4">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    {{-- Empleado con buscador --}}
                    <div class="space-y-1 col-span-2">
                        <x-input-label for="buscarEmpleado" value="Empleado" class="text-xs" />

                        <div class="relative" wire:click.away="ocultarDropdown">
                            <input
                                id="buscarEmpleado"
                                wire:model.live.debounce.300ms="buscarEmpleado"
                                type="text"
                                class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                                placeholder="Buscar por nombre, apellido o documento..."
                                autocomplete="off">

                            {{-- Dropdown de resultados --}}
                            @if($mostrarDropdown && !empty($buscarEmpleado))
                            <div class="absolute z-50 w-full mt-1 bg-white border border-gray-300 rounded-md shadow-lg max-h-60 overflow-y-auto">
                                @if($empleados->count() > 0)
                                @foreach($empleados as $emp)
                                <div
                                    wire:click="seleccionarEmpleado({{ $emp->id }})"
                                    class="px-4 py-2 hover:bg-gray-100 cursor-pointer text-sm flex items-center gap-2">
                                    <span class="font-medium">{{ $emp->persona->nombres ?? '' }} {{ $emp->persona->apellidos ?? '' }}</span>
                                    <span class="text-gray-500 text-xs">({{ $emp->persona->numero_documento ?? '-' }})</span>
                                    @if($emp->empresa)
                                    <span class="ml-auto text-xs text-gray-400">{{ $emp->empresa->nombre ?? '' }}</span>
                                    @endif
                                </div>
                                @endforeach
                                @else
                                <div class="px-4 py-2 text-sm text-gray-500">No se encontraron empleados</div>
                                @endif
                            </div>
                            @endif
                        </div>

                        {{-- Contenedor con altura fija para evitar saltos --}}
                        <div class="h-6 flex items-center">
                            @if($empleado_id)
                            {{-- Mostrar empleado seleccionado --}}
                            @php
                            $empleadoSeleccionado = \App\Models\Empleado::with('persona')->find($empleado_id);
                            @endphp
                            @if($empleadoSeleccionado)
                            <div class="text-sm text-gray-600">
                                <span class="font-medium">Seleccionado:</span>
                                {{ $empleadoSeleccionado->persona->nombres ?? '' }} {{ $empleadoSeleccionado->persona->apellidos ?? '' }}
                                ({{ $empleadoSeleccionado->persona->numero_documento ?? '-' }})
                            </div>
                            @endif
                            @else
                            {{-- Texto por defecto --}}
                            <div class="text-sm text-gray-400">
                                <span class="font-medium">Seleccionado:</span>
                                <span class="italic">Ningún empleado seleccionado</span>
                            </div>
                            @endif
                        </div>

                        <x-input-error :messages="$errors->get('empleado_id')" class="text-[10px]" />
                    </div>

                    {{-- Fecha --}}
                    <div class="space-y-1">
                        <x-input-label for="fecha" value="Fecha" class="text-xs" />
                        <input id="fecha" wire:model="fecha" type="date" class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                        <x-input-error :messages="$errors->get('fecha')" class="text-[10px]" />
                    </div>

                    {{-- Tipo Movimiento --}}
                    <div class="space-y-1">
                        <x-input-label for="tipo_movimiento" value="Tipo Movimiento" class="text-xs" />
                        <select id="tipo_movimiento" wire:model.live="tipo_movimiento" class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                            @foreach($tipos as $tipo)
                            <option value="{{ $tipo->value }}">{{ $tipo->label() }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('tipo_movimiento')" class="text-[10px]" />
                    </div>

                    {{-- Monto --}}
                    <div class="space-y-1">
                        <x-input-label for="monto" value="Monto (Gs.)" class="text-xs" />
                        <input id="monto" wire:model.live="monto" type="number" min="0" step="1" class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" placeholder="Ej: 200000">
                        <x-input-error :messages="$errors->get('monto')" class="text-[10px]" />
                    </div>

                    {{-- Año / Mes --}}
                    <div class="grid grid-cols-2 gap-2">
                        <div class="space-y-1">
                            <x-input-label for="anio" value="Año" class="text-xs" />
                            <input id="anio" wire:model="anio" type="number" class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                        </div>
                        <div class="space-y-1">
                            <x-input-label for="mes" value="Mes" class="text-xs" />
                            <input id="mes" wire:model="mes" type="number" min="1" max="12" class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                        </div>
                    </div>

                    {{-- Observación --}}
                    <div class="space-y-1 col-span-2">
                        <x-input-label for="observacion" value="Observación" class="text-xs" />
                        <input id="observacion" wire:model="observacion" type="text" class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" placeholder="Requerido solo para OTROS">
                        <x-input-error :messages="$errors->get('observacion')" class="text-[10px]" />
                    </div>
                </div>

                {{-- Acciones --}}
                <div class="flex justify-end gap-2 pt-3 border-t border-border">
                    <x-secondary-button type="button" x-on:click="$dispatch('close-modal', { name: 'nomina-form-modal' })" class="text-xs h-9 px-3">
                        Cancelar
                    </x-secondary-button>
                    <x-primary-button type="submit" wire:loading.attr="disabled" class="text-xs h-10 px-4">
                        {{ $modoEdicion ? 'Actualizar' : 'Guardar' }}
                    </x-primary-button>
                </div>
            </form>
        </div>
    </x-modal>
</div>