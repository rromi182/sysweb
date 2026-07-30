<?php

namespace App\Livewire\Empresas;

use Livewire\Component;
use App\Models\Empresa;
use Livewire\Attributes\On;

class EmpresaDetail extends Component
{
    public ?Empresa $empresa = null;

    public function render()
    {
        return view('livewire.empresas.empresa-detail');
    }

    #[On('show-empresa')]
    public function show(Empresa $empresa)
    {
        $this->empresa = $empresa;
        $this->dispatch('open-modal', name: 'empresa-detail-modal');
    }

    public function closeModal()
    {
        $this->empresa = null;
    }
}
