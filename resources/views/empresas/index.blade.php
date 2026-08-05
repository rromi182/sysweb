<x-app-layout title="Empresas">
    <x-slot name="header">
    <div class="space-y-6">
        {{-- Header de la página --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="font-semibold text-xl text-foreground leading-tight">
                    {{ __('Empresas') }}
                </h2>
                <p class="text-muted-foreground text-sm">
                    Gestiona las empresas registradas en el sistema
                </p>
            </div>
            <x-primary-button x-data x-on:click="$dispatch('create-empresa')">
                <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                {{ __('Crear Empresa') }}
            </x-primary-button>
        </div>

        {{-- Card que envuelve la tabla --}}
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <livewire:empresas.empresa-table />
        </div>
    </div>

    <livewire:empresas.empresa-form />
    <livewire:empresas.empresa-detail />
</x-app-layout>