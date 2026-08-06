<x-app-layout>
    <div class="space-y-4">
        {{-- Header de página --}}
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-xl font-semibold tracking-tight">Empleados</h2>
                <p class="text-sm text-muted-foreground">Gestiona el personal de tu empresa</p>
            </div>
            <button 
                onclick="Livewire.dispatch('create-empleado')"
                class="inline-flex items-center justify-center rounded-md bg-primary px-3 py-1.5 text-xs font-medium text-primary-foreground shadow hover:bg-primary/90 h-8"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                Nuevo Empleado
            </button>
        </div>

        {{-- Card wide --}}
        <div class="rounded-lg border bg-card text-card-foreground shadow-sm">
            <livewire:empleados.empleado-table />
        </div>
    </div>
</x-app-layout>