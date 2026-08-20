<x-app-layout>
    <div class="space-y-4">

        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold tracking-tight">
                    Movimientos de Nómina
                </h2>

                <p class="text-sm text-muted-foreground">
                    Registro de salarios, extras, vales y descuentos
                </p>
            </div>

            <x-primary-button
                x-data
                x-on:click="$dispatch('crear-movimiento')"
            >
                <x-heroicon-o-plus class="w-4 h-4 mr-2" />
                {{ __('Nuevo Movimiento') }}
            </x-primary-button>
        </div>

        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <livewire:nominas.nomina-table />
        </div>

    </div>

    <livewire:nominas.nomina-form />
</x-app-layout>