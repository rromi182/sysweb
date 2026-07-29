<header class="sticky top-0 z-30 h-12 flex items-center gap-3 px-4 bg-background border-b border-border shrink-0">

    {{-- Toggle sidebar --}}
    <button
        @click="$dispatch('toggle-sidebar')"
        class="flex items-center justify-center w-7 h-7 rounded-md border border-input
               hover:bg-muted text-muted-foreground transition-colors"
        aria-label="Toggle menú"
    >
        <x-heroicon-o-bars-3 class="w-4 h-4" />
    </button>

    <span class="flex-1"></span>

    {{-- Usuario --}}
    <x-dropdown align="right" width="48">
        <x-slot name="trigger">
            <button class="flex items-center gap-2 rounded-full ring-offset-background
                           focus-visible:outline-none focus-visible:ring-2 focus-visible:ring-ring focus-visible:ring-offset-2">
                <span class="hidden md:block text-sm font-medium text-foreground">{{ Auth::user()->name }}</span>
                <x-avatar :name="Auth::user()->name" />
                <x-heroicon-o-chevron-down class="w-3.5 h-3.5 text-muted-foreground" />
            </button>
        </x-slot>
        <x-slot name="content">
            <x-dropdown-link :href="route('profile.index')" wire:navigate  :active="request()->routeIs('profile.*')">
                Perfil
            </x-dropdown-link>
            <x-dropdown-link :href="route('settings.index')" wire:navigate  :active="request()->routeIs('settings.*')">
                Settings
            </x-dropdown-link>
            <form method="POST" action="{{ route('logout') }}">
                @csrf
                <x-dropdown-link :href="route('logout')"
                    onclick="event.preventDefault(); this.closest('form').submit();">
                    Cerrar sesión
                </x-dropdown-link>
            </form>
        </x-slot>
    </x-dropdown>

</header>

{{-- Overlay mobile: cierra el sidebar al tocar afuera --}}
<div
    x-show="open"
    x-transition:enter="transition duration-200 ease-out"
    x-transition:enter-start="opacity-0"
    x-transition:enter-end="opacity-100"
    x-transition:leave="transition duration-150 ease-in"
    x-transition:leave-start="opacity-100"
    x-transition:leave-end="opacity-0"
    class="fixed inset-0 z-30 bg-black/40 backdrop-blur-sm lg:hidden"
    @click="$dispatch('toggle-sidebar')"
    style="display:none"
    x-cloak
></div>