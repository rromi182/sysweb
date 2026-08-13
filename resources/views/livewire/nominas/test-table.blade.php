<div>
    <div class="p-4">
        <input wire:model.live="search" type="text" placeholder="Buscar empleado..." 
               class="w-full rounded-md border border-gray-300 px-3 py-2 text-sm">
    </div>

    <table class="w-full text-sm text-left">
        <thead class="bg-gray-50 text-gray-700 uppercase text-xs">
            <tr>
                <th class="px-4 py-3">Empleado</th>
                <th class="px-4 py-3">Tipo</th>
                <th class="px-4 py-3">Monto</th>
                <th class="px-4 py-3">Fecha</th>
                <th class="px-4 py-3">Concepto</th>
            </tr>
        </thead>
        <tbody>
            @forelse($movimientos as $mov)
                <tr class="border-b hover:bg-gray-50">
                    <td class="px-4 py-3">{{ $mov->empleado->nombre }} {{ $mov->empleado->apellido }}</td>
                    <td class="px-4 py-3 capitalize">{{ $mov->tipo }}</td>
                    <td class="px-4 py-3">{{ number_format($mov->monto, 0, ',', '.') }}</td>
                    <td class="px-4 py-3">{{ $mov->fecha->format('d/m/Y') }}</td>
                    <td class="px-4 py-3">{{ $mov->concepto ?? '-' }}</td>
                </tr>
            @empty
                <tr>
                    <td colspan="5" class="px-4 py-3 text-center text-gray-500">No hay movimientos registrados</td>
                </tr>
            @endforelse
        </tbody>
    </table>

    <div class="p-4">
        {{ $movimientos->links() }}
    </div>
</div>