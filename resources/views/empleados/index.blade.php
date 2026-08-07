<x-app-layout>
    <div class="space-y-4">
        {{-- Header de página --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold tracking-tight"> Base Colaboradores</h2>
                <p class="text-sm text-muted-foreground">Gestiona el personal de tu empresa</p>
            </div>
            <x-primary-button x-data x-on:click="$dispatch('create-empleado')">
                <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                {{ __('Nuevo Empleado') }}
            </x-primary-button>
        </div>

        {{-- Card wide --}}
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <livewire:empleados.empleado-table />
        </div>
    </div>
     <livewire:empleados.empleado-form />
     <livewire:empleados.empleado-detail />
</x-app-layout>