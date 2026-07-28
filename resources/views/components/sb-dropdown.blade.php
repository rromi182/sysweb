@props(['label', 'icon', 'active' => false])

<div x-data="{ open: {{ $active ? 'true' : 'false' }} }">
    <button
        @click="open = !open"
        class="flex items-center w-full gap-2.5 rounded-md px-3 py-2 text-sm transition-colors
               {{ $active ? 'text-foreground font-medium' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}"
    >
        <x-dynamic-component
            :component="'heroicon-o-' . $icon"
            class="w-4 h-4 shrink-0"
        />
        <span class="flex-1 text-left">{{ $label }}</span>
        <x-heroicon-o-chevron-right
            class="w-3.5 h-3.5 shrink-0 transition-transform duration-200 ease-[cubic-bezier(.4,0,.2,1)]"
            ::class="open ? 'rotate-90' : ''"
        />
    </button>

    <div
        x-show="open"
        x-collapse
        x-cloak
    >
        <div class="ml-3 mt-0.5 pl-3 border-l border-border space-y-0.5 py-0.5">
            {{ $slot }}
        </div>
    </div>
</div>