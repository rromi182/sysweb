@props(['href', 'active' => false, 'icon', 'label', 'forceLabel' => false])

<a href="{{ $href }}"
   title="{{ $label }}"
   class="flex items-center gap-2.5 rounded-md px-2 py-1.5 text-sm transition-colors whitespace-nowrap overflow-hidden
          {{ $active
              ? 'bg-accent/50 text-accent-foreground font-medium'
              : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}"
>
    <i class="ti ti-{{ $icon }} text-base shrink-0" aria-hidden="true"></i>
    <span
        class="truncate transition-[opacity,width] duration-[250ms] ease-[cubic-bezier(.4,0,.2,1)]"
        @if(!$forceLabel) :class="$parent.collapsed ? 'opacity-0 w-0' : 'opacity-100'" @endif
    >
        {{ $label }}
    </span>
</a>