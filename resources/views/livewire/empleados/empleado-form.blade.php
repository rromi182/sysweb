<x-modal name="empleado-form-modal" :title="''" maxWidth="3xl">
    <div class="p-4 sm:p-6">
        <div class="mb-4 space-y-1 text-center sm:text-left border-b border-gray-200 pb-3">
            <h3 class="text-base sm:text-lg font-semibold leading-none tracking-tight text-foreground">
                {{ $isEditing ? 'Editar Empleado' : 'Nuevo Empleado' }}
            </h3>
            <p class="text-xs sm:text-sm text-muted-foreground">
                {{ $isEditing ? 'Realiza cambios en los datos del empleado.' : 'Registra un nuevo empleado en el sistema.' }}
            </p>
        </div>

        <form wire:submit="save" class="space-y-3">
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                {{-- Persona --}}
                <div class="space-y-1">
                    <x-input-label for="persona_id" :value="__('Persona *')" class="text-sm" />
                    <select id="persona_id" wire:model="persona_id"
                        class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50">
                        <option value="">Seleccionar...</option>
                        @foreach($personas as $persona)
                            <option value="{{ $persona->id }}">{{ $persona->nombre_completo }} ({{ $persona->numero_documento }})</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('persona_id')" />
                </div>

                {{-- Código Empleado --}}
                <x-form-input name="codigo_empleado" label="Código Empleado *" type="text"
                    wire:model="codigo_empleado" placeholder="Ej. EMP001" required class="text-sm" />

                {{-- Empresa --}}
                <div class="space-y-1">
                    <x-input-label for="empresa_id" :value="__('Empresa *')" class="text-sm" />
                    <select id="empresa_id" wire:model.live="empresa_id"
                        class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                        <option value="">Seleccionar...</option>
                        @foreach($empresas as $emp)
                            <option value="{{ $emp->id }}">{{ $emp->nombre }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('empresa_id')" />
                </div>

                {{-- Sucursal --}}
                <div class="space-y-1">
                    <x-input-label for="sucursal_id" :value="__('Sucursal *')" class="text-sm" />
                    <select id="sucursal_id" wire:model="sucursal_id"
                        class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                        <option value="">Seleccionar...</option>
                        @foreach($sucursales as $suc)
                            <option value="{{ $suc->id }}">{{ $suc->nombre }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('sucursal_id')" />
                </div>

                {{-- Departamento --}}
                <div class="space-y-1">
                    <x-input-label for="departamento_id" :value="__('Departamento')" class="text-sm" />
                    <select id="departamento_id" wire:model="departamento_id"
                        class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                        <option value="">Seleccionar...</option>
                        @foreach($departamentos as $dep)
                            <option value="{{ $dep->id }}">{{ $dep->nombre }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('departamento_id')" />
                </div>

                {{-- Cargo --}}
                <div class="space-y-1">
                    <x-input-label for="cargo_id" :value="__('Cargo *')" class="text-sm" />
                    <select id="cargo_id" wire:model="cargo_id"
                        class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                        <option value="">Seleccionar...</option>
                        @foreach($cargos as $cargo)
                            <option value="{{ $cargo->id }}">{{ $cargo->nombre }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('cargo_id')" />
                </div>

                {{-- Tipo Contrato --}}
                <div class="space-y-1">
                    <x-input-label for="tipo_contrato_id" :value="__('Tipo Contrato')" class="text-sm" />
                    <select id="tipo_contrato_id" wire:model="tipo_contrato_id"
                        class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                        <option value="">Seleccionar...</option>
                        @foreach($tiposContrato as $tc)
                            <option value="{{ $tc->id }}">{{ $tc->nombre }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('tipo_contrato_id')" />
                </div>

                {{-- Horario --}}
                <div class="space-y-1">
                    <x-input-label for="horario_id" :value="__('Horario Laboral')" class="text-sm" />
                    <select id="horario_id" wire:model="horario_id"
                        class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                        <option value="">Seleccionar...</option>
                        @foreach($horarios as $hor)
                            <option value="{{ $hor->id }}">{{ $hor->nombre }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('horario_id')" />
                </div>

                {{-- Fecha Ingreso --}}
                <x-form-input name="fecha_ingreso" label="Fecha Ingreso *" type="date"
                    wire:model="fecha_ingreso" required class="text-sm" />

                {{-- Fecha Egreso --}}
                <x-form-input name="fecha_egreso" label="Fecha Egreso" type="date"
                    wire:model="fecha_egreso" class="text-sm" />

                {{-- Estado --}}
                <div class="space-y-1">
                    <x-input-label for="estado" :value="__('Estado')" class="text-sm" />
                    <select id="estado" wire:model="estado"
                        class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                        @foreach($estados as $est)
                            <option value="{{ $est }}">{{ ucfirst($est) }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('estado')" />
                </div>

                {{-- Salario Base --}}
                <x-form-input name="salario_base" label="Salario Base *" type="number"
                    wire:model="salario_base" placeholder="Ej. 3500000" required class="text-sm" />

                {{-- Número IPS --}}
                <x-form-input name="numero_ips" label="Número IPS" type="text"
                    wire:model="numero_ips" placeholder="Ej. 1234567" class="text-sm" />

                {{-- Profesión --}}
                <x-form-input name="profesion" label="Profesión" type="text"
                    wire:model="profesion" placeholder="Ej. Contador" class="text-sm" />

                {{-- Jefe Inmediato --}}
                <div class="space-y-1">
                    <x-input-label for="jefe_inmediato_id" :value="__('Jefe Inmediato')" class="text-sm" />
                    <select id="jefe_inmediato_id" wire:model="jefe_inmediato_id"
                        class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                        <option value="">Ninguno</option>
                        @foreach($jefes as $jefe)
                            <option value="{{ $jefe->id }}">{{ $jefe->nombre_completo }}</option>
                        @endforeach
                    </select>
                    <x-input-error :messages="$errors->get('jefe_inmediato_id')" />
                </div>
            </div>

            {{-- Acciones --}}
            <div class="mt-4 flex justify-end gap-2 border-t border-gray-200 pt-3">
                <x-secondary-button type="button" x-on:click="$dispatch('close-modal', { name: 'empleado-form-modal' })" class="text-sm px-3 py-1.5">
                    {{ __('Cancelar') }}
                </x-secondary-button>
                <x-primary-button type="submit" wire:loading.attr="disabled" class="text-sm px-4 py-1.5">
                    <x-heroicon-o-check wire:loading.remove wire:target="save" class="w-3.5 h-3.5 mr-1.5" />
                    {{ $isEditing ? __('Guardar') : __('Crear') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-modal>