<div>
    <x-dialog-modal wire:model="open">
        <x-slot name="title">
            {{ $modoEdicion ? 'Editar Movimiento' : 'Nuevo Movimiento' }}
        </x-slot>

        <x-slot name="content">
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="col-span-2">
                    <x-label for="empleado_id" value="Empleado" />
                    <select id="empleado_id" wire:model="empleado_id" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        <option value="">Seleccione...</option>
                        @foreach($empleados as $emp)
                            <option value="{{ $emp->id }}">
                                {{ $emp->persona->numero_documento ?? '-' }} - {{ $emp->persona->nombres ?? '' }} {{ $emp->persona->apellidos ?? '' }}
                            </option>
                        @endforeach
                    </select>
                    <x-input-error for="empleado_id" class="mt-1" />
                </div>

                <div>
                    <x-label for="fecha" value="Fecha" />
                    <x-input id="fecha" type="date" wire:model="fecha" class="mt-1 block w-full" />
                    <x-input-error for="fecha" class="mt-1" />
                </div>

                <div>
                    <x-label for="tipo_movimiento" value="Tipo Movimiento" />
                    <select id="tipo_movimiento" wire:model="tipo_movimiento" class="mt-1 block w-full rounded-md border-gray-300 dark:border-gray-600 dark:bg-gray-700 dark:text-white shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                        @foreach($tipos as $tipo)
                            <option value="{{ $tipo->value }}">{{ $tipo->label() }}</option>
                        @endforeach
                    </select>
                    <x-input-error for="tipo_movimiento" class="mt-1" />
                </div>

                <div>
                    <x-label for="monto" value="Monto (Gs.)" />
                    <x-input id="monto" type="number" wire:model="monto" min="0" step="1" class="mt-1 block w-full" placeholder="Ej: 200000" />
                    <x-input-error for="monto" class="mt-1" />
                </div>

                <div class="grid grid-cols-2 gap-2">
                    <div>
                        <x-label for="anio" value="Año" />
                        <x-input id="anio" type="number" wire:model="anio" class="mt-1 block w-full" />
                    </div>
                    <div>
                        <x-label for="mes" value="Mes" />
                        <x-input id="mes" type="number" wire:model="mes" min="1" max="12" class="mt-1 block w-full" />
                    </div>
                </div>

                <div class="col-span-2">
                    <x-label for="observacion" value="Observación" />
                    <x-input id="observacion" type="text" wire:model="observacion" class="mt-1 block w-full" placeholder="Requerido solo para OTROS" />
                    <x-input-error for="observacion" class="mt-1" />
                </div>
            </div>
        </x-slot>

        <x-slot name="footer">
            <x-secondary-button wire:click="$set('open', false)" wire:loading.attr="disabled">
                Cancelar
            </x-secondary-button>

            <x-primary-button class="ml-2" wire:click="save" wire:loading.attr="disabled">
                {{ $modoEdicion ? 'Actualizar' : 'Guardar' }}
            </x-primary-button>
        </x-slot>
    </x-dialog-modal>
</div>
