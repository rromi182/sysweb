<?php

namespace App\Livewire\Empresas;

use Livewire\Component;
use App\Models\Empresa;
use Livewire\Attributes\On;

class EmpresaDetail extends Component
{
    public ?Empresa $empresa = null;
    public $sucursalesCount = 0;
    public $funcionariosCount = 0;
    public $departamentosCount = 0;
    public $cargosCount = 0;

    public function render()
    {
        return view('livewire.empresas.empresa-detail');
    }

    #[On('show-empresa')]
    public function show(int $empresaId): void
    {
        $this->empresa = Empresa::with([
            'sucursales', 
            'funcionarios', 
            'departamentos',
            'cargos',
            'creador',
            'actualizador'
        ])->find($empresaId);

        if ($this->empresa) {
            $this->sucursalesCount = $this->empresa->sucursales->count();
            $this->funcionariosCount = $this->empresa->funcionarios->count();
            $this->departamentosCount = $this->empresa->departamentos->count();
            $this->cargosCount = $this->empresa->cargos->count();
            $this->dispatch('open-modal', name: 'empresa-detail-modal');
        } else {
            $this->dispatch('toast', message: 'Empresa no encontrada', type: 'error');
        }
    }

    #[On('close-detail')]
    public function closeModal(): void
    {
        $this->empresa = null;
        $this->sucursalesCount = 0;
        $this->funcionariosCount = 0;
        $this->departamentosCount = 0;
        $this->cargosCount = 0;
        $this->dispatch('close-modal', name: 'empresa-detail-modal');
    }

    public function getLogoUrl(): ?string
    {
        if ($this->empresa && $this->empresa->logo) {
            return asset('storage/empresas/' . $this->empresa->logo);
        }
        return null;
    }

    public function getEstadoBadgeAttribute(): string
    {
        if (!$this->empresa) {
            return '';
        }

        return $this->empresa->estado 
            ? '<span class="px-2 py-1 text-xs rounded-full bg-green-100 text-green-800">Activo</span>'
            : '<span class="px-2 py-1 text-xs rounded-full bg-red-100 text-red-800">Inactivo</span>';
    }
}