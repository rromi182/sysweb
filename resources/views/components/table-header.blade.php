@props(['title' => null, 'createEvent' => null, 'createLabel' => 'Nuevo'])

<div class="flex flex-col gap-4 p-4 sm:flex-row sm:items-center sm:justify-between">
    <div class="flex items-center gap-2">
        @if($title)
            <h3 class="text-sm font-semibold">{{ $title }}</h3>
        @endif
    </div>
    
    <div class="flex items-center gap-2">
        {{-- Slot para filtros adicionales --}}
        {{ $slot }}
        
        @if($createEvent)
            <button 
                onclick="Livewire.dispatch('{{ $createEvent }}')"
                class="inline-flex h-8 items-center justify-center rounded-md bg-primary px-3 text-xs font-medium text-primary-foreground shadow transition-colors hover:bg-primary/90"
            >
                <svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" class="mr-1.5"><path d="M5 12h14"/><path d="M12 5v14"/></svg>
                {{ $createLabel }}
            </button>
        @endif
    </div>
</div>