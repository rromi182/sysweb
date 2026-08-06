{{-- Este componente se inyecta arriba de la tabla de PowerGrid --}}
<div class="flex flex-col gap-3 p-4 pb-0 sm:flex-row sm:items-center sm:justify-between">
    
    {{-- Búsqueda estilo shadcn --}}
    <div class="relative w-full sm:w-72">
        <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="absolute left-2.5 top-2.5 text-muted-foreground"><circle cx="11" cy="11" r="8"/><path d="m21 21-4.3-4.3"/></svg>
        <input 
            type="search" 
            wire:model.live.debounce.300ms="search"
            placeholder="Buscar..."
            class="flex h-8 w-full rounded-md border border-input bg-background pl-8 pr-3 text-xs shadow-sm transition-colors placeholder:text-muted-foreground focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring"
        >
    </div>

    {{-- Acciones: Exportar + Filtros --}}
    <div class="flex items-center gap-2">
        
        {{-- Botón de filtros (si usás filtros inline) --}}
        @if($this->hasColumnFilters)
            <button 
                wire:click="toggleFilters"
                class="inline-flex h-8 items-center justify-center gap-1.5 rounded-md border border-input bg-background px-3 text-xs font-medium shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 3H2l8 9.46V19l4 2v-8.54L22 3z"/></svg>
                Filtros
            </button>
        @endif

        {{-- Exportar --}}
        <div class="relative" x-data="{ open: false }">
            <button 
                @click="open = !open"
                class="inline-flex h-8 items-center justify-center gap-1.5 rounded-md border border-input bg-background px-3 text-xs font-medium shadow-sm transition-colors hover:bg-accent hover:text-accent-foreground"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"/><polyline points="7 10 12 15 17 10"/><line x1="12" x2="12" y1="15" y2="3"/></svg>
                Exportar
                <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="ml-0.5"><path d="m6 9 6 6 6-6"/></svg>
            </button>
            
            <div 
                x-show="open" 
                @click.away="open = false"
                class="absolute right-0 mt-1 w-32 rounded-md border bg-popover shadow-md"
                style="display: none;"
            >
                <button wire:click="exportToXLS" class="flex w-full items-center px-3 py-2 text-xs hover:bg-accent hover:text-accent-foreground">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                    Excel
                </button>
                <button wire:click="exportToCsv" class="flex w-full items-center px-3 py-2 text-xs hover:bg-accent hover:text-accent-foreground">
                    <svg xmlns="http://www.w3.org/2000/svg" width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" class="mr-2"><path d="M14.5 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V7.5L14.5 2z"/><polyline points="14 2 14 8 20 8"/></svg>
                    CSV
                </button>
            </div>
        </div>

    </div>
</div>