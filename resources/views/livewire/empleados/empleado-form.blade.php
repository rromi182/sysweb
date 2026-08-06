<x-modal name="empleado-form-modal" :title="''" maxWidth="7xl">
    <div class="p-6">
        {{-- Header --}}
        <div class="mb-4 pb-3 border-b border-border">
            <h3 class="text-lg font-semibold text-foreground">
                {{ $isEditing ? 'Editar Empleado' : 'Nuevo Empleado' }}
            </h3>
            <p class="text-sm text-muted-foreground">
                {{ $isEditing ? 'Modifica los datos del empleado.' : 'Completa los datos para registrar un nuevo empleado.' }}
            </p>
        </div>

        <form wire:submit="save" class="space-y-5">
            {{-- ═══════════════════════════════════════════ --}}
            {{-- SECCIÓN: DATOS PERSONALES                   --}}
            {{-- ═══════════════════════════════════════════ --}}
            <div>
                <h4 class="text-xs font-semibold uppercase tracking-wider text-muted-foreground mb-2 flex items-center gap-1.5">
                    <x-heroicon-o-user class="w-3.5 h-3.5" />
                    Datos Personales
                </h4>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 gap-x-4 gap-y-3">
                    {{-- Nombres --}}
                    <div class="space-y-1">
                        <x-input-label for="nombres" value="Nombres *" class="text-xs" />
                        <input id="nombres" wire:model="nombres" type="text"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                            placeholder="Ej. Juan Carlos">
                        <x-input-error :messages="$errors->get('nombres')" class="text-[10px]" />
                    </div>

                    {{-- Apellidos --}}
                    <div class="space-y-1">
                        <x-input-label for="apellidos" value="Apellidos *" class="text-xs" />
                        <input id="apellidos" wire:model="apellidos" type="text"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                            placeholder="Ej. Pérez Gómez">
                        <x-input-error :messages="$errors->get('apellidos')" class="text-[10px]" />
                    </div>

                    {{-- Tipo Documento --}}
                    <div class="space-y-1">
                        <x-input-label for="tipo_documento" value="Tipo Doc. *" class="text-xs" />
                        <select id="tipo_documento" wire:model="tipo_documento"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                            @foreach($tiposDocumento as $td)
                                <option value="{{ $td }}">{{ $td }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('tipo_documento')" class="text-[10px]" />
                    </div>

                    {{-- Número Documento --}}
                    <div class="space-y-1">
                        <x-input-label for="numero_documento" value="Nro. Documento *" class="text-xs" />
                        <input id="numero_documento" wire:model="numero_documento" type="text"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                            placeholder="Ej. 1234567">
                        <x-input-error :messages="$errors->get('numero_documento')" class="text-[10px]" />
                    </div>

                    {{-- Fecha Nacimiento --}}
                    <div class="space-y-1">
                        <x-input-label for="fecha_nacimiento" value="Fecha Nac." class="text-xs" />
                        <input id="fecha_nacimiento" wire:model="fecha_nacimiento" type="date"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                        <x-input-error :messages="$errors->get('fecha_nacimiento')" class="text-[10px]" />
                    </div>

                    {{-- Sexo --}}
                    <div class="space-y-1">
                        <x-input-label for="sexo" value="Sexo *" class="text-xs" />
                        <select id="sexo" wire:model="sexo"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                            @foreach($sexos as $key => $label)
                                <option value="{{ $key }}">{{ $label }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('sexo')" class="text-[10px]" />
                    </div>

                    {{-- Teléfono --}}
                    <div class="space-y-1">
                        <x-input-label for="telefono" value="Teléfono" class="text-xs" />
                        <input id="telefono" wire:model="telefono" type="text"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                            placeholder="Ej. 0981123456">
                        <x-input-error :messages="$errors->get('telefono')" class="text-[10px]" />
                    </div>

                    {{-- Correo --}}
                    <div class="space-y-1">
                        <x-input-label for="correo" value="Correo" class="text-xs" />
                        <input id="correo" wire:model="correo" type="email"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                            placeholder="Ej. juan@email.com">
                        <x-input-error :messages="$errors->get('correo')" class="text-[10px]" />
                    </div>

                    {{-- Dirección --}}
                    <div class="space-y-1 col-span-2 sm:col-span-3 lg:col-span-4">
                        <x-input-label for="direccion" value="Dirección" class="text-xs" />
                        <input id="direccion" wire:model="direccion" type="text"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                            placeholder="Ej. Av. Mcal. López 1234">
                        <x-input-error :messages="$errors->get('direccion')" class="text-[10px]" />
                    </div>
                </div>
            </div>

            {{-- ═══════════════════════════════════════════ --}}
            {{-- SECCIÓN: DATOS LABORALES                    --}}
            {{-- ═══════════════════════════════════════════ --}}
            <div>
                <h4 class="text-xs font-semibold uppercase tracking-wider text-muted-foreground mb-2 flex items-center gap-1.5">
                    <x-heroicon-o-briefcase class="w-3.5 h-3.5" />
                    Datos Laborales
                </h4>
                <div class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-5 gap-x-4 gap-y-3">
                    {{-- Código Empleado --}}
                    <div class="space-y-1">
                        <x-input-label for="codigo_empleado" value="Código *" class="text-xs" />
                        <input id="codigo_empleado" wire:model="codigo_empleado" type="text"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                            placeholder="EMP001">
                        <x-input-error :messages="$errors->get('codigo_empleado')" class="text-[10px]" />
                    </div>

                    {{-- Empresa --}}
                    <div class="space-y-1">
                        <x-input-label for="empresa_id" value="Empresa *" class="text-xs" />
                        <select id="empresa_id" wire:model.live="empresa_id"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                            <option value="">Seleccionar...</option>
                            @foreach($empresas as $emp)
                                <option value="{{ $emp->id }}">{{ $emp->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('empresa_id')" class="text-[10px]" />
                    </div>

                    {{-- Sucursal --}}
                    <div class="space-y-1">
                        <x-input-label for="sucursal_id" value="Sucursal *" class="text-xs" />
                        <select id="sucursal_id" wire:model="sucursal_id"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                            <option value="">Seleccionar...</option>
                            @foreach($sucursales as $suc)
                                <option value="{{ $suc->id }}">{{ $suc->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('sucursal_id')" class="text-[10px]" />
                    </div>

                    {{-- Departamento --}}
                    <div class="space-y-1">
                        <x-input-label for="departamento_id" value="Departamento" class="text-xs" />
                        <select id="departamento_id" wire:model="departamento_id"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                            <option value="">Seleccionar...</option>
                            @foreach($departamentos as $dep)
                                <option value="{{ $dep->id }}">{{ $dep->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('departamento_id')" class="text-[10px]" />
                    </div>

                    {{-- Cargo --}}
                    <div class="space-y-1">
                        <x-input-label for="cargo_id" value="Cargo *" class="text-xs" />
                        <select id="cargo_id" wire:model="cargo_id"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                            <option value="">Seleccionar...</option>
                            @foreach($cargos as $cargo)
                                <option value="{{ $cargo->id }}">{{ $cargo->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('cargo_id')" class="text-[10px]" />
                    </div>

                    {{-- Tipo Contrato --}}
                    <div class="space-y-1">
                        <x-input-label for="tipo_contrato_id" value="Contrato" class="text-xs" />
                        <select id="tipo_contrato_id" wire:model="tipo_contrato_id"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                            <option value="">Seleccionar...</option>
                            @foreach($tiposContrato as $tc)
                                <option value="{{ $tc->id }}">{{ $tc->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('tipo_contrato_id')" class="text-[10px]" />
                    </div>

                    {{-- Horario --}}
                    <div class="space-y-1">
                        <x-input-label for="horario_id" value="Horario" class="text-xs" />
                        <select id="horario_id" wire:model="horario_id"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                            <option value="">Seleccionar...</option>
                            @foreach($horarios as $hor)
                                <option value="{{ $hor->id }}">{{ $hor->nombre }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('horario_id')" class="text-[10px]" />
                    </div>

                    {{-- Fecha Ingreso --}}
                    <div class="space-y-1">
                        <x-input-label for="fecha_ingreso" value="Fecha Ingreso *" class="text-xs" />
                        <input id="fecha_ingreso" wire:model="fecha_ingreso" type="date"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                        <x-input-error :messages="$errors->get('fecha_ingreso')" class="text-[10px]" />
                    </div>

                    {{-- Fecha Egreso --}}
                    <div class="space-y-1">
                        <x-input-label for="fecha_egreso" value="Fecha Egreso" class="text-xs" />
                        <input id="fecha_egreso" wire:model="fecha_egreso" type="date"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                        <x-input-error :messages="$errors->get('fecha_egreso')" class="text-[10px]" />
                    </div>

                    {{-- Salario Base --}}
                    <div class="space-y-1">
                        <x-input-label for="salario_base" value="Salario Gs. *" class="text-xs" />
                        <input id="salario_base" wire:model="salario_base" type="number"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                            placeholder="3500000">
                        <x-input-error :messages="$errors->get('salario_base')" class="text-[10px]" />
                    </div>

                    {{-- Número IPS --}}
                    <div class="space-y-1">
                        <x-input-label for="numero_ips" value="Nro. IPS" class="text-xs" />
                        <input id="numero_ips" wire:model="numero_ips" type="text"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                            placeholder="1234567">
                        <x-input-error :messages="$errors->get('numero_ips')" class="text-[10px]" />
                    </div>

                    {{-- Profesión --}}
                    <div class="space-y-1">
                        <x-input-label for="profesion" value="Profesión" class="text-xs" />
                        <input id="profesion" wire:model="profesion" type="text"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
                            placeholder="Ej. Contador">
                        <x-input-error :messages="$errors->get('profesion')" class="text-[10px]" />
                    </div>

                    {{-- Jefe Inmediato --}}
                    <div class="space-y-1">
                        <x-input-label for="jefe_inmediato_id" value="Jefe Inmediato" class="text-xs" />
                        <select id="jefe_inmediato_id" wire:model="jefe_inmediato_id"
                            class="h-10 w-full rounded-md border border-input bg-background px-3 text-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                            <option value="">Ninguno</option>
                            @foreach($jefes as $jefe)
                                <option value="{{ $jefe->id }}">{{ $jefe->nombre_completo }}</option>
                            @endforeach
                        </select>
                        <x-input-error :messages="$errors->get('jefe_inmediato_id')" class="text-[10px]" />
                    </div>
                </div>
            </div>

            {{-- Acciones --}}
            <div class="flex justify-end gap-2 pt-3 border-t border-border">
                <x-secondary-button type="button" x-on:click="$dispatch('close-modal', { name: 'empleado-form-modal' })" class="text-xs h-9 px-3">
                    Cancelar
                </x-secondary-button>
                <x-primary-button type="submit" wire:loading.attr="disabled" class="text-xs h-10 px-4">
                    <x-heroicon-o-check wire:loading.remove wire:target="save" class="w-3.5 h-3.5 mr-1" />
                    {{ $isEditing ? 'Guardar Cambios' : 'Crear Empleado' }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-modal>