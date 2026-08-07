<div class="space-y-6" x-data="{ tab: 'activos' }">
    {{-- Header minimalista --}}
    <div class="flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4">
        <div>
            <h1 class="text-2xl font-bold tracking-tight text-foreground">Recursos Humanos</h1>
            <p class="text-sm text-muted-foreground mt-1">Panel de gestión de Recursos Humanos</p>
        </div>
        <div class="flex items-center gap-2">
            <a href="{{ route('empleados.index') }}" wire:navigate
               class="inline-flex items-center gap-2 rounded-md border border-input bg-background px-4 py-2 text-sm font-medium shadow-sm hover:bg-accent hover:text-accent-foreground transition-colors">
                <x-heroicon-o-users class="w-4 h-4" />
                Ver todos
            </a>
        </div>
    </div>

    {{-- KPIs Cards modernas --}}
    <div class="grid grid-cols-1 gap-4 sm:grid-cols-2 lg:grid-cols-4">
        {{-- Total Activos --}}
        <div class="bg-card rounded-xl border border-border p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Colaboladores Activos</p>
                    <p class="mt-2 text-3xl font-bold text-foreground">{{ number_format($this->stats['total_empleados']) }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <x-heroicon-o-users class="h-5 w-5 text-primary" />
                </div>
            </div>
            <div class="mt-4 flex items-center text-xs text-muted-foreground">
                <x-heroicon-o-arrow-trending-up class="w-3.5 h-3.5 mr-1 text-emerald-500" />
                <span class="text-emerald-600 font-medium">{{ $this->stats['nuevos_este_mes'] }}</span>
                <span class="ml-1">nuevos este mes</span>
            </div>
        </div>

        {{-- Salario Promedio --}}
        <div class="bg-card rounded-xl border border-border p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Salario Promedio</p>
                    <p class="mt-2 text-3xl font-bold text-foreground">
                        Gs. {{ number_format($this->stats['salario_promedio'], 0, ',', '.') }}
                    </p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <x-heroicon-o-banknotes class="h-5 w-5 text-primary" />
                </div>
            </div>
            <div class="mt-4 text-xs text-muted-foreground">Basado en empleados activos</div>
        </div>

        {{-- Departamentos --}}
        <div class="bg-card rounded-xl border border-border p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Departamentos</p>
                    <p class="mt-2 text-3xl font-bold text-foreground">{{ $this->stats['departamentos'] }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <x-heroicon-o-building-office class="h-5 w-5 text-primary" />
                </div>
            </div>
            <div class="mt-4 text-xs text-muted-foreground">Unidades organizativas activas</div>
        </div>

        {{-- Tasa de retención (cálculo simple) --}}
        <div class="bg-card rounded-xl border border-border p-5 shadow-sm hover:shadow-md transition-shadow">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm font-medium text-muted-foreground">Nuevos Ingresos</p>
                    <p class="mt-2 text-3xl font-bold text-foreground">{{ $this->stats['nuevos_este_mes'] }}</p>
                </div>
                <div class="flex h-10 w-10 items-center justify-center rounded-lg bg-primary/10">
                    <x-heroicon-o-user-plus class="h-5 w-5 text-primary" />
                </div>
            </div>
            <div class="mt-4 text-xs text-muted-foreground">Este mes · {{ now()->format('F Y') }}</div>
        </div>
    </div>

    {{-- Filtros y Tabla de Empleados --}}
    <div class="bg-card rounded-xl border border-border shadow-sm overflow-hidden">
        <div class="p-5 border-b border-border">
            <div class="flex flex-col lg:flex-row lg:items-center justify-between gap-4">
                <h3 class="text-sm font-semibold text-foreground flex items-center gap-2">
                    <x-heroicon-o-user-group class="w-4 h-4 text-muted-foreground" />
                    Colaboladores Recientes
                </h3>
                
                <div class="flex flex-wrap items-center gap-2">
                    {{-- Búsqueda --}}
                    <div class="relative">
                        <x-heroicon-o-magnifying-glass class="absolute left-2.5 top-1/2 -translate-y-1/2 w-4 h-4 text-muted-foreground" />
                        <input wire:model.live.debounce.300ms="search" type="text" placeholder="Buscar colaborador..." 
                               class="h-9 w-[220px] rounded-md border border-input bg-background pl-9 pr-3 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                    </div>

                    {{-- Filtro Estado --}}
                    <select wire:model.live="estadoFilter" class="h-9 rounded-md border border-input bg-background px-3 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                        <option value="activo">Activos</option>
                        <option value="vacaciones">Vacaciones</option>
                        <option value="licencia">Licencia</option>
                        <option value="suspendido">Suspendidos</option>
                        <option value="inactivo">Inactivos</option>
                    </select>

                    {{-- Filtro Departamento --}}
                    <select wire:model.live="departamentoFilter" class="h-9 rounded-md border border-input bg-background px-3 text-sm shadow-sm focus-visible:outline-none focus-visible:ring-1 focus-visible:ring-ring">
                        <option value="">Todos los deptos.</option>
                        @foreach($this->departamentosList as $depto)
                            <option value="{{ $depto->id }}">{{ $depto->nombre }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>

        {{-- Tabla minimalista --}}
        <div class="overflow-x-auto">
            <table class="w-full text-sm text-left">
                <thead class="bg-muted/50 text-muted-foreground uppercase text-xs font-semibold">
                    <tr>
                        <th class="px-5 py-3">Colaborador</th>
                        <th class="px-5 py-3">Documento</th>
                        <th class="px-5 py-3">Departamento</th>
                        <th class="px-5 py-3">Cargo</th>
                        <th class="px-5 py-3">Contrato</th>
                        <th class="px-5 py-3">Ingreso</th>
                        <th class="px-5 py-3 text-right">Salario</th>
                        <th class="px-5 py-3 text-center">Estado</th>
                    </tr>
                </thead>
                <tbody class="divide-y divide-border">
                    @forelse($this->empleados as $emp)
                        <tr class="hover:bg-muted/30 transition-colors group">
                            <td class="px-5 py-3">
                                <div class="flex items-center gap-3">
                                    <x-avatar :name="$emp->nombre_completo" class="w-8 h-8 text-xs" />
                                    <div>
                                        <p class="font-medium text-foreground">{{ $emp->nombre_completo }}</p>
                                        <p class="text-xs text-muted-foreground">{{ $emp->codigo_empleado }}</p>
                                    </div>
                                </div>
                            </td>
                            <td class="px-5 py-3 text-muted-foreground">{{ $emp->documento }}</td>
                            <td class="px-5 py-3 text-muted-foreground">{{ $emp->departamento->nombre ?? '-' }}</td>
                            <td class="px-5 py-3 text-muted-foreground">{{ $emp->cargo->nombre ?? '-' }}</td>
                            <td class="px-5 py-3 text-muted-foreground">{{ $emp->tipoContrato->nombre ?? '-' }}</td>
                            <td class="px-5 py-3 text-muted-foreground">{{ $emp->fecha_ingreso?->format('d/m/Y') }}</td>
                            <td class="px-5 py-3 text-right font-medium text-foreground">
                                Gs. {{ number_format($emp->salario_base, 0, ',', '.') }}
                            </td>
                            <td class="px-5 py-3 text-center">
                                {!! $emp->estado_badge !!}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="px-5 py-8 text-center text-muted-foreground">
                                No se encontraron empleados con los filtros aplicados.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($this->empleados->count() >= 10)
            <div class="px-5 py-3 border-t border-border bg-muted/20 text-center">
                <a href="{{ route('empleados.index') }}" wire:navigate class="text-sm font-medium text-primary hover:underline">
                    Ver todos los empleados →
                </a>
            </div>
        @endif
    </div>

    {{-- Gráficos y distribución --}}
    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        {{-- Distribución por Departamento --}}
        <div class="bg-card rounded-xl border border-border p-5 shadow-sm lg:col-span-2">
            <h3 class="text-sm font-semibold text-foreground mb-4 flex items-center gap-2">
                <x-heroicon-o-chart-pie class="w-4 h-4 text-muted-foreground" />
                Distribución por Departamento
            </h3>
            <div class="space-y-3">
                @forelse($this->stats['por_departamento'] as $item)
                    @php $pct = $this->stats['total_empleados'] > 0 ? round(($item->total / $this->stats['total_empleados']) * 100) : 0; @endphp
                    <div>
                        <div class="flex justify-between text-sm mb-1">
                            <span class="text-foreground font-medium">{{ $item->departamento->nombre ?? 'Sin Depto.' }}</span>
                            <span class="text-muted-foreground">{{ $item->total }} ({{ $pct }}%)</span>
                        </div>
                        <div class="h-2 w-full rounded-full bg-muted overflow-hidden">
                            <div class="h-full rounded-full bg-foreground transition-all duration-700" style="width: {{ $pct }}%"></div>
                        </div>
                    </div>
                @empty
                    <p class="text-sm text-muted-foreground">No hay datos disponibles</p>
                @endforelse
            </div>
        </div>

        {{-- Tipos de Contrato --}}
        <div class="bg-card rounded-xl border border-border p-5 shadow-sm">
            <h3 class="text-sm font-semibold text-foreground mb-4 flex items-center gap-2">
                <x-heroicon-o-document-text class="w-4 h-4 text-muted-foreground" />
                Tipos de Contrato
            </h3>
            <div class="space-y-4">
                @forelse($this->stats['por_contrato'] as $item)
                    <div class="flex items-center justify-between p-3 rounded-lg bg-muted/50 border border-border">
                        <div class="flex items-center gap-3">
                            <div class="h-8 w-8 rounded-full bg-primary/10 flex items-center justify-center">
                                <x-heroicon-o-shield-check class="w-4 h-4 text-primary" />
                            </div>
                            <div>
                                <p class="text-sm font-medium text-foreground">{{ $item->tipoContrato->nombre ?? 'N/A' }}</p>
                                <p class="text-xs text-muted-foreground">Empleados</p>
                            </div>
                        </div>
                        <span class="text-lg font-bold text-foreground">{{ $item->total }}</span>
                    </div>
                @empty
                    <p class="text-sm text-muted-foreground">No hay datos</p>
                @endforelse
            </div>
        </div>
    </div>

    
</div>