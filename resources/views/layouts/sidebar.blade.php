@persist('sidebar')
<aside x-data="{ open: $persist(true).as('sb') }" @open-sidebar.window="open = true" @toggle-sidebar.window="open = !open"
    :class="open ? 'w-60' : 'w-0 -translate-x-full lg:w-0 lg:translate-x-0'"
    class="fixed inset-y-0 left-0 z-40 flex flex-col bg-background border-r border-border
           transition-[width,transform] duration-300 ease-[cubic-bezier(.4,0,.2,1)] overflow-hidden
           lg:relative lg:translate-x-0 lg:shrink-0"
    x-cloak>
    {{-- Logo --}}
    {{-- Logo --}}
    <div class="flex items-center justify-center h-12 px-4 border-b border-border">
        <a href="{{ route('dashboard') }}" wire:navigate class="flex items-center">
            <img src="{{ asset('images/logo-tit-2.jpg') }}" alt="{{ config('app.name') }}"
                class="h-8 w-auto object-contain" />
        </a>
    </div>

    {{-- Nav --}}
    <nav class="flex-1 overflow-y-auto overflow-x-hidden py-2 px-2 space-y-0.5 w-60">

        {{-- Dashboard 
        <a href="{{ route('dashboard') }}" wire:navigate
            class="flex items-center gap-2.5 rounded-md px-3 py-2 text-sm transition-colors
                  {{ request()->routeIs('dashboard') ? 'bg-accent/50 text-accent-foreground font-medium' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
            <x-heroicon-o-squares-2x2 class="w-4 h-4 shrink-0" />
            <span>Panel de control GRAL</span>
        </a>--}}

        <a href="{{ route('dashboard.hr') }}" wire:navigate
            class="flex items-center gap-2.5 rounded-md px-3 py-2 text-sm transition-colors
                  {{ request()->routeIs('dashboard.hr') ? 'bg-accent/50 text-accent-foreground font-medium' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
            <x-heroicon-o-squares-2x2 class="w-4 h-4 shrink-0" />
            <span>Panel de control RRHH</span>
        </a>


        {{-- Recursos Humanos --}}
        <x-sb-dropdown label="Recursos humanos" icon="user-group" :active="request()->routeIs(['dashboard.hr', 'customers.*'])">
            <x-sb-link :href="route('empleados.index')" :active="request()->routeIs('empleados.index')" label="Empleados" />
            <x-sb-link :href="route('sales.index')" :active="request()->routeIs(['sales.index', 'sales.show'])" label="Asistencias" />
            <x-sb-link :href="route('customers.index')" :active="request()->routeIs('customers.*')" label="Contratos" />
            <x-sb-link :href="route('nomina.index')" :active="request()->routeIs('nomina.*')" label="Movimientos de Nómina" />
        </x-sb-dropdown>

        {{-- Ventas 
        <x-sb-dropdown label="Ventas" icon="banknotes" :active="request()->routeIs(['sales.*', 'customers.*'])">
            <x-sb-link :href="route('sales.create')" :active="request()->routeIs('sales.create')" label="POS" />
            <x-sb-link :href="route('sales.index')" :active="request()->routeIs(['sales.index', 'sales.show'])" label="Ventas" />
            <x-sb-link :href="route('customers.index')" :active="request()->routeIs('customers.*')" label="Clientes" />
        </x-sb-dropdown>--}}

        {{-- Compras 
        <x-sb-dropdown label="Compras" icon="shopping-cart" :active="request()->routeIs(['purchases.*', 'suppliers.*'])">
            <x-sb-link :href="route('purchases.index')" :active="request()->routeIs('purchases.*')" label="Pedidos" />
            <x-sb-link :href="route('suppliers.index')" :active="request()->routeIs('suppliers.*')" label="Proveedores" />
        </x-sb-dropdown>--}}

        {{-- Productos 
        <x-sb-dropdown label="Productos" icon="cube" :active="request()->routeIs(['products.*', 'categories.*', 'units.*'])">
            <x-sb-link :href="route('products.index')" :active="request()->routeIs('products.*')" label="Productos" />
            <x-sb-link :href="route('categories.index')" :active="request()->routeIs('categories.*')" label="Categoría" />
            <x-sb-link :href="route('units.index')" :active="request()->routeIs('units.*')" label="Unidades" />
        </x-sb-dropdown>--}}

        {{-- Usuarios --}}
        <a href="{{ route('users.index') }}" wire:navigate
            class="flex items-center gap-2.5 rounded-md px-3 py-2 text-sm transition-colors
                  {{ request()->routeIs('users.*') ? 'bg-accent/50 text-accent-foreground font-medium' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
            <x-heroicon-o-users class="w-4 h-4 shrink-0" />
            <span>Usuarios</span>
        </a>
        {{-- Empresas --}}
        <a href="{{ route('empresas.index') }}" wire:navigate
            class="flex items-center gap-2.5 rounded-md px-3 py-2 text-sm transition-colors
          {{ request()->routeIs('master.empresas.*') || request()->routeIs('rrhh.empresas.*') ? 'bg-accent/50 text-accent-foreground font-medium' : 'text-muted-foreground hover:bg-muted hover:text-foreground' }}">
            <x-heroicon-o-building-office class="w-4 h-4 shrink-0" />
            <span>Empresas</span>
        </a>

    </nav>
</aside>
@endpersist