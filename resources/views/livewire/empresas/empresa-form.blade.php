<x-modal name="empresa-form-modal" :title="''" maxWidth="3xl">
    <div class="p-4 sm:p-6">
        <!-- Custom Header - Más compacto -->
        <div class="mb-4 space-y-1 text-center sm:text-left border-b border-gray-200 pb-3">
            <h3 class="text-base sm:text-lg font-semibold leading-none tracking-tight text-foreground">
                {{ $isEditing ? 'Editar Empresa' : 'Crear Empresa' }}
            </h3>
            <p class="text-xs sm:text-sm text-muted-foreground">
                {{ $isEditing ? 'Realiza cambios en la empresa.' : 'Agrega una nueva empresa al sistema.' }}
            </p>
        </div>

        <form wire:submit="save" class="space-y-3">
            <!-- Grid de 2 columnas para campos principales -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-3">
                <!-- Nombre -->
                <div class="sm:col-span-2">
                    <x-form-input
                        name="nombre"
                        label="Nombre *"
                        type="text"
                        wire:model="nombre"
                        placeholder="Ej. Tech Solutions S.A."
                        required
                        class="text-sm"
                    />
                </div>

                <!-- Razón Social -->
                <x-form-input
                    name="razon_social"
                    label="Razón Social"
                    type="text"
                    wire:model="razon_social"
                    placeholder="Ej. Tech Solutions S.A."
                    class="text-sm"
                />

                <!-- RUC -->
                <x-form-input
                    name="ruc"
                    label="RUC"
                    type="text"
                    wire:model="ruc"
                    placeholder="Ej. 80012345-6"
                    class="text-sm"
                />

                <!-- Teléfono -->
                <x-form-input
                    name="telefono"
                    label="Teléfono"
                    type="text"
                    wire:model="telefono"
                    placeholder="Ej. (021) 123-4567"
                    class="text-sm"
                />

                <!-- Correo -->
                <x-form-input
                    name="correo"
                    label="Correo"
                    type="email"
                    wire:model="correo"
                    placeholder="Ej. info@techsolutions.com"
                    class="text-sm"
                />

                <!-- Sitio Web -->
                <x-form-input
                    name="sitio_web"
                    label="Sitio Web"
                    type="url"
                    wire:model="sitio_web"
                    placeholder="Ej. https://www.techsolutions.com"
                    class="text-sm"
                />

                <!-- Estado -->
                <div class="space-y-1">
                    <x-input-label for="estado" :value="__('Estado')" class="text-sm" />
                    <select
                        id="estado"
                        wire:model="estado"
                        class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm ring-offset-background focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    >
                        <option value="1">Activo</option>
                        <option value="0">Inactivo</option>
                    </select>
                    <x-input-error :messages="$errors->get('estado')" />
                </div>

                <!-- Logo - Ocupa menos espacio -->
                <div class="space-y-1">
                    <x-input-label for="logo" :value="__('Logo')" class="text-sm" />
                    <input
                        id="logo"
                        type="file"
                        wire:model="logo"
                        accept="image/*"
                        class="flex h-9 w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm file:mr-3 file:py-1 file:px-3 file:rounded-md file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    />
                    <x-input-error :messages="$errors->get('logo')" />
                </div>
            </div>

            <!-- Dirección - Ocupa toda la fila -->
            <div class="space-y-1">
                <x-input-label for="direccion" :value="__('Dirección')" class="text-sm" />
                <textarea
                    id="direccion"
                    wire:model="direccion"
                    rows="2"
                    class="flex min-h-[50px] w-full rounded-md border border-input bg-background px-3 py-1.5 text-sm ring-offset-background placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2 disabled:cursor-not-allowed disabled:opacity-50"
                    placeholder="Dirección completa..."
                ></textarea>
                <x-input-error :messages="$errors->get('direccion')" />
            </div>

            <!-- Logo Preview - Más compacto -->
            @if($logo_preview)
                <div class="flex items-center gap-3 p-2 bg-gray-50 rounded-md border border-gray-200">
                    <div class="relative">
                        <img src="{{ $logo_preview }}" alt="Logo preview" class="h-12 w-12 rounded-full object-cover border-2 border-gray-200">
                        <button type="button" wire:click="removeLogo" 
                                class="absolute -top-1.5 -right-1.5 bg-red-500 text-white rounded-full p-0.5 hover:bg-red-600 transition">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                    <div>
                        <p class="text-xs font-medium text-gray-700">Vista previa del logo</p>
                        <p class="text-xs text-gray-500">Haz clic en la X para eliminar</p>
                    </div>
                </div>
            @endif

            <!-- Actions - Más compacto -->
            <div class="mt-4 flex justify-end gap-2 border-t border-gray-200 pt-3">
                <x-secondary-button type="button" x-on:click="$dispatch('close-modal', { name: 'empresa-form-modal' })" class="text-sm px-3 py-1.5">
                    {{ __('Cancelar') }}
                </x-secondary-button>

                <x-primary-button type="submit" wire:loading.attr="disabled" class="text-sm px-4 py-1.5">
                    <svg wire:loading wire:target="save" class="animate-spin -ml-1 mr-1.5 h-3.5 w-3.5 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24">
                        <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                        <path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path>
                    </svg>
                    <x-heroicon-o-check wire:loading.remove wire:target="save" class="w-3.5 h-3.5 mr-1.5" />
                    {{ $isEditing ? __('Guardar') : __('Crear') }}
                </x-primary-button>
            </div>
        </form>
    </div>
</x-modal>