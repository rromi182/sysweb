<x-app-layout>
    <div class="space-y-4">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold tracking-tight">Resumen del Colaborador</h2>
                <p class="text-sm text-muted-foreground">Período: {{ now()->format('m/Y') }}</p>
            </div>
            <x-primary-button :href="route('nomina.index')" wire:navigate>
                <x-heroicon-o-arrow-left class="w-4 h-4 mr-2" />
                {{ __('Volver a Movimientos') }}
            </x-primary-button>
        </div>

        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <livewire:nominas.nomina-resumen />
        </div>
    </div>
</x-app-layout>
