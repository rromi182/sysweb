<div>
    <div class="flex flex-wrap items-center gap-4 mb-6">
        <div>
            <x-label for="anio" value="Año" />
            <x-input type="number" wire:model.live="anio" class="mt-1 w-32" />
        </div>
        <div>
            <x-label for="mes" value="Mes" />
            <x-input type="number" wire:model.live="mes" min="1" max="12" class="mt-1 w-32" />
        </div>
        <div class="ml-auto text-right">
            <div class="text-sm text-muted-foreground">Total Neto General</div>
            <div class="text-2xl font-bold">
                Gs. {{ number_format($this->totalGeneral, 0, ',', '.') }}
            </div>
        </div>
    </div>

    <div class="overflow-x-auto">
        <table class="min-w-full text-sm text-left">
            <thead class="text-xs uppercase bg-muted">
                <tr>
                    <th class="px-4 py-3">Nombre y Apellido</th>
                    <th class="px-4 py-3 text-right">Sueldo</th>
                    <th class="px-4 py-3 text-right">Extra</th>
                    <th class="px-4 py-3 text-right">Vale</th>
                    <th class="px-4 py-3 text-right">Ausencia</th>
                    <th class="px-4 py-3 text-right">Llegada Tardía</th>
                    <th class="px-4 py-3 text-right">Otros</th>
                    <th class="px-4 py-3 text-right font-bold">Total Neto</th>
                </tr>
            </thead>
            <tbody>
                @forelse($this->resumen as $empleadoId => $fila)
                    <tr class="border-b hover:bg-muted/50">
                        <td class="px-4 py-3 font-medium">{{ $fila['nombre'] }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($fila['sueldo'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-green-600">{{ number_format($fila['extra'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-red-600">{{ number_format($fila['vale'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-red-600">{{ number_format($fila['ausencia'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right text-red-600">{{ number_format($fila['llegada_tardia'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right">{{ number_format($fila['otros'], 0, ',', '.') }}</td>
                        <td class="px-4 py-3 text-right font-bold">{{ number_format($fila['total_neto'], 0, ',', '.') }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="px-4 py-6 text-center text-muted-foreground">
                            No hay movimientos registrados para el período seleccionado.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
