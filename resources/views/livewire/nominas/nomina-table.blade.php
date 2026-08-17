{{-- resources/views/livewire/nominas/nomina-table.blade.php --}}
<div>
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="p-4 border-b">
            <h3 class="text-lg font-semibold text-gray-800">Movimientos de Nómina</h3>
            <p class="text-sm text-gray-500">Total de registros: {{ $this->total }}</p>
        </div>
        
        {{-- Aquí se renderiza la tabla de PowerGrid --}}
        {{ $this->table }}
    </div>
</div>