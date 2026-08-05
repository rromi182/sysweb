<x-app-layout title="Empleados">
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-foreground leading-tight">
                {{ __('Empleados') }}
            </h2>
            <x-primary-button x-data x-on:click="$dispatch('create-empleado')">
                <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                {{ __('Nuevo Empleado') }}
            </x-primary-button>
        </div>
    </x-slot>

    <div class="py-4">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <livewire:empleados.empleado-table />
        </div>
    </div>

    <livewire:empleados.empleado-form />
    <livewire:empleados.empleado-detail />
</x-app-layout>