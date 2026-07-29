@props(['href', 'active' => false, 'label'])

<a href="{{ $href }}"
wire:navigate
   wire:current="bg-accent/50 text-accent-foreground font-medium"
   class="block rounded-md px-3 py-1.5 text-sm transition-colors
          {{ $active ? 'bg-accent/50 text-accent-foreground font-medium' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}"
>
    {{ $label }}
</a>