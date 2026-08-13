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
                    {{-- Empleado --}}
                    <div class="space-y-1 col-span-2">
                        <x-input-label for="empleado_id" value="Empleado" class="text-xs" />
                        <select id="empleado_id" wire:model="empleado_id" class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                            <option value="">Seleccione...</option>
                            @foreach($empleados as $emp)
                                <option value="{{ $emp->id }}">
                                    {{ $emp->persona->numero_documento ?? '-' }} - {{ $emp->persona->nombres ?? '' }} {{ $emp->persona->apellidos ?? '' }}
                                </option>
                            @endforeach
                        </select>
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
                        <select id="tipo_movimiento" wire:model="tipo_movimiento" class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                            @foreach($tipos as $tipo)
                                <option value="{{ $tipo->value }}">{{ $tipo->label() }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('tipo_movimiento')" class="text-[10px]" />
                    </div>

                    {{-- Monto --}}
                    <div class="space-y-1">
                        <x-input-label for="monto" value="Monto (Gs.)" class="text-xs" />
                        <input id="monto" wire:model="monto" type="number" min="0" step="1" class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring" placeholder="Ej: 200000">
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