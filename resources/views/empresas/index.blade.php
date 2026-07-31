<x-app-layout title="Empresas">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-foreground leading-tight">
                {{ __('Empresas') }}
            </h2>
            <x-primary-button x-data x-on:click="$dispatch('create-empresa')">
                <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                {{ __('Crear Empresa') }}
            </x-primary-button>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:empresas.empresa-table />
        </div>
    </div>

    <livewire:empresas.empresa-form />
    <livewire:empresas.empresa-detail />
</x-app-layout>