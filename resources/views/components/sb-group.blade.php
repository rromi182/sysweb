@props(['label', 'forceLabel' => false])

<div class="pt-2">
    <p class="px-2 mb-0.5 text-[10px] font-medium uppercase tracking-wider text-muted-foreground whitespace-nowrap
              transition-[opacity] duration-[250ms] ease-[cubic-bezier(.4,0,.2,1)]"
       @if(!$forceLabel) :class="$parent.collapsed ? 'opacity-0' : 'opacity-100'" @endif
    >
        {{ $label }}
    </p>
    {{ $slot }}
</div>