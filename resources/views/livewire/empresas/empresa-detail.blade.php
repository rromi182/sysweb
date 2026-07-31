<!-- resources/views/livewire/empresas/empresa-detail.blade.php -->
<x-modal name="empresa-detail-modal" focusable>
    @if($empresa)
        <div class="p-6">
            <!-- Header -->
            <div class="mb-6 space-y-1.5 text-center sm:text-left border-b border-gray-200 pb-4">
                <div class="flex items-center justify-between">
                    <h3 class="text-lg font-semibold leading-none tracking-tight text-foreground">
                        {{ __('Detalles de la Empresa') }}
                    </h3>
                    @if($this->getLogoUrl())
                        <img src="{{ $this->getLogoUrl() }}" alt="Logo de {{ $empresa->nombre }}" 
                             class="h-12 w-12 rounded-full object-cover border-2 border-gray-200">
                    @endif
                </div>
                <p class="text-sm text-muted-foreground">
                    {{ __('Información detallada de') }} {{ $empresa->nombre }}.
                </p>
            </div>

            <div class="space-y-6">
                <div class="space-y-1">
                    <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Nombre') }}</label>
                    <p class="text-sm text-foreground font-medium">{{ $empresa->nombre }}</p>
                </div>

                @if($empresa->razon_social)
                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Razón Social') }}</label>
                        <p class="text-sm text-foreground font-medium">{{ $empresa->razon_social }}</p>
                    </div>
                @endif

                @if($empresa->ruc)
                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('RUC') }}</label>
                        <p class="text-sm text-foreground font-medium">{{ $empresa->ruc }}</p>
                    </div>
                @endif

                @if($empresa->direccion)
                    <div class="space-y-1">
                        <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Dirección') }}</label>
                        <p class="text-sm text-foreground font-medium">{{ $empresa->direccion }}</p>
                    </div>
                @endif

                <div class="space-y-1">
                    <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Estado') }}</label>
                    <p class="text-sm text-foreground font-medium">{!! $this->getEstadoBadgeAttribute() !!}</p>
                </div>

                <div class="grid grid-cols-1 gap-6 sm:grid-cols-2">
                    @if($empresa->telefono)
                        <div class="space-y-1">
                            <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Teléfono') }}</label>
                            <p class="text-sm text-foreground font-medium">{{ $empresa->telefono }}</p>
                        </div>
                    @endif

                    @if($empresa->correo)
                        <div class="space-y-1">
                            <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Correo') }}</label>
                            <p class="text-sm text-foreground font-medium">{{ $empresa->correo }}</p>
                        </div>
                    @endif

                    @if($empresa->sitio_web)
                        <div class="space-y-1">
                            <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Sitio Web') }}</label>
                            <p class="text-sm text-foreground font-medium">
                                <a href="{{ $empresa->sitio_web }}" target="_blank" rel="noopener noreferrer" class="text-blue-600 hover:text-blue-800 hover:underline">
                                    {{ $empresa->sitio_web }}
                                </a>
                            </p>
                        </div>
                    @endif
                </div>

                <!-- Estadísticas -->
                <div class="border-t border-gray-200 pt-4">
                    <h4 class="text-sm font-medium text-muted-foreground mb-3">{{ __('Estadísticas') }}</h4>
                    <div class="grid grid-cols-2 gap-3 sm:grid-cols-4">
                        <div class="bg-blue-50 rounded-lg p-3 text-center">
                            <p class="text-2xl font-bold text-blue-600">{{ $sucursalesCount }}</p>
                            <p class="text-xs text-gray-600">{{ __('Sucursales') }}</p>
                        </div>
                        <div class="bg-green-50 rounded-lg p-3 text-center">
                            <p class="text-2xl font-bold text-green-600">{{ $funcionariosCount }}</p>
                            <p class="text-xs text-gray-600">{{ __('Funcionarios') }}</p>
                        </div>
                        <div class="bg-purple-50 rounded-lg p-3 text-center">
                            <p class="text-2xl font-bold text-purple-600">{{ $departamentosCount }}</p>
                            <p class="text-xs text-gray-600">{{ __('Departamentos') }}</p>
                        </div>
                        <div class="bg-orange-50 rounded-lg p-3 text-center">
                            <p class="text-2xl font-bold text-orange-600">{{ $cargosCount }}</p>
                            <p class="text-xs text-gray-600">{{ __('Cargos') }}</p>
                        </div>
                    </div>
                </div>

                <!-- Meta -->
                <div class="border-t border-gray-200 pt-4">
                    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2">
                        <div class="space-y-1">
                            <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Creado por') }}</label>
                            <p class="text-sm text-foreground font-medium">{{ $empresa->creador?->name ?? 'N/A' }}</p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Fecha de creación') }}</label>
                            <p class="text-sm text-foreground font-medium">{{ $empresa->created_at?->format('d M Y, H:i') ?? '-' }}</p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Actualizado por') }}</label>
                            <p class="text-sm text-foreground font-medium">{{ $empresa->actualizador?->name ?? 'N/A' }}</p>
                        </div>
                        <div class="space-y-1">
                            <label class="text-sm font-medium leading-none text-muted-foreground">{{ __('Última actualización') }}</label>
                            <p class="text-sm text-foreground font-medium">{{ $empresa->updated_at?->format('d M Y, H:i') ?? '-' }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Actions -->
            <div class="mt-6 flex items-center justify-end gap-x-2 pt-4 border-t border-border">
                <x-secondary-button type="button" x-on:click="$dispatch('close-modal', { name: 'empresa-detail-modal' })">
                    {{ __('Cerrar') }}
                </x-secondary-button>
                <x-primary-button type="button" x-on:click="$dispatch('close-modal', { name: 'empresa-detail-modal' }); $dispatch('edit-empresa', { empresa: {{ $empresa->id }} })">
                    <x-heroicon-o-pencil-square class="w-4 h-4 mr-2" />
                    {{ __('Editar Empresa') }}
                </x-primary-button>
            </div>
        </div>
    @else
        <div class="p-8 text-center flex flex-col items-center justify-center space-y-3">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div>
            <span class="text-sm text-muted-foreground">{{ __('Cargando detalles...') }}</span>
        </div>
    @endif
</x-modal>