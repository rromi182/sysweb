<x-modal name="empleado-detail-modal" title="Detalles del Empleado" maxWidth="2xl">
    @if($empleado)
        <div class="p-6 space-y-4">
            <div class="flex items-center gap-4 border-b border-gray-200 pb-4">
                <div class="w-16 h-16 rounded-full bg-accent/10 flex items-center justify-center text-2xl font-bold text-accent">
                    {{ strtoupper(substr($empleado->persona?->nombres, 0, 1)) }}
                </div>
                <div>
                    <h3 class="text-lg font-semibold text-foreground">{{ $empleado->nombre_completo }}</h3>
                    <p class="text-sm text-muted-foreground">{{ $empleado->cargo?->nombre }} — {{ $empleado->empresa?->nombre }}</p>
                </div>
            </div>

            <div class="grid grid-cols-2 gap-4 text-sm">
                <div>
                    <span class="text-muted-foreground">Código:</span>
                    <p class="font-medium">{{ $empleado->codigo_empleado }}</p>
                </div>
                <div>
                    <span class="text-muted-foreground">Documento:</span>
                    <p class="font-medium">{{ $empleado->documento }}</p>
                </div>
                <div>
                    <span class="text-muted-foreground">Sucursal:</span>
                    <p class="font-medium">{{ $empleado->sucursal?->nombre ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-muted-foreground">Departamento:</span>
                    <p class="font-medium">{{ $empleado->departamento?->nombre ?? 'N/A' }}</p>
                </div>
                <div>
                    <span class="text-muted-foreground">Fecha Ingreso:</span>
                    <p class="font-medium">{{ $empleado->fecha_ingreso?->format('d/m/Y') }}</p>
                </div>
                <div>
                    <span class="text-muted-foreground">Salario Base:</span>
                    <p class="font-medium">{{ format_money($empleado->salario_base) }}</p>
                </div>
                <div>
                    <span class="text-muted-foreground">Estado:</span>
                    <p class="font-medium">{!! $empleado->estado_badge !!}</p>
                </div>
                <div>
                    <span class="text-muted-foreground">N° IPS:</span>
                    <p class="font-medium">{{ $empleado->numero_ips ?? 'N/A' }}</p>
                </div>
            </div>

            <div class="flex justify-end pt-4 border-t border-gray-200">
                <x-secondary-button x-on:click="$dispatch('close-detail')" class="text-sm">
                    Cerrar
                </x-secondary-button>
            </div>
        </div>
    @endif
</x-modal>