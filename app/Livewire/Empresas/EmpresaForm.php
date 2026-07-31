<?php

namespace App\Livewire\Empresas;

use Livewire\Component;
use Livewire\WithFileUploads;
use App\Models\Empresa;
use App\Services\EmpresaService;
use App\DTOs\EmpresaData;
use Livewire\Attributes\On;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmpresaForm extends Component
{
    use WithFileUploads;

    public ?Empresa $empresa = null;
    public bool $isEditing = false;
    
    // Form fields
    public string $nombre = '';
    public ?string $razon_social = '';
    public ?string $ruc = '';
    public ?string $direccion = '';
    public ?string $telefono = '';
    public ?string $correo = '';
    public ?string $sitio_web = '';
    public int $estado = 1;
    public $logo = null;
    public ?string $logo_preview = null;

    protected function rules(): array
    {
        $rules = [
            'nombre' => 'required|string|max:100',
            'razon_social' => 'nullable|string|max:100',
            'ruc' => 'nullable|string|max:20|unique:empresas,ruc',
            'direccion' => 'nullable|string|max:255',
            'telefono' => 'nullable|string|max:20',
            'correo' => 'nullable|email|max:100',
            'sitio_web' => 'nullable|url|max:100',
            'estado' => 'required|boolean',
            'logo' => 'nullable|image|max:2048',
        ];

        // Ignore unique rule for RUC when editing
        if ($this->isEditing && $this->empresa) {
            $rules['ruc'] = 'nullable|string|max:20|unique:empresas,ruc,' . $this->empresa->id;
        }

        return $rules;
    }

    protected function messages(): array
    {
        return [
            'nombre.required' => 'El nombre de la empresa es obligatorio',
            'ruc.unique' => 'Este RUC ya está registrado en otra empresa',
            'correo.email' => 'El correo electrónico no es válido',
            'sitio_web.url' => 'La URL del sitio web no es válida',
            'logo.image' => 'El archivo debe ser una imagen',
            'logo.max' => 'La imagen no debe superar los 2MB',
        ];
    }

    public function mount(?int $empresaId = null): void
    {
        if ($empresaId) {
            $this->empresa = Empresa::find($empresaId);
            
            if ($this->empresa) {
                $this->isEditing = true;
                $this->fillFields($this->empresa);
            }
        }
    }

    public function fillFields(Empresa $empresa): void
    {
        $this->nombre = $empresa->nombre;
        $this->razon_social = $empresa->razon_social;
        $this->ruc = $empresa->ruc;
        $this->direccion = $empresa->direccion;
        $this->telefono = $empresa->telefono;
        $this->correo = $empresa->correo;
        $this->sitio_web = $empresa->sitio_web;
        $this->estado = $empresa->estado;
        
        if ($empresa->logo) {
            $this->logo_preview = asset('storage/empresas/' . $empresa->logo);
        }
    }

    public function save(EmpresaService $service): void
    {
        $this->validate();

        try {
            $data = $this->prepareData();

            if ($this->isEditing && $this->empresa) {
                $empresa = $service->updateEmpresa($this->empresa, $data);
                $message = 'Empresa actualizada exitosamente';
                $this->dispatch('empresaUpdated');
            } else {
                $empresa = $service->createEmpresa($data);
                $message = 'Empresa creada exitosamente';
                $this->dispatch('empresaCreated');
            }

            $this->dispatch('toast', message: $message, type: 'success');
            $this->dispatch('close-modal', name: 'empresa-form-modal');
            $this->resetForm();
            
        } catch (\Exception $e) {
            Log::error('Error saving empresa: ' . $e->getMessage(), [
                'trace' => $e->getTraceAsString()
            ]);
            
            $this->dispatch('toast', 
                message: 'Error al guardar empresa: ' . $e->getMessage(), 
                type: 'error'
            );
        }
    }

    protected function prepareData(): EmpresaData
    {
        $data = [
            'nombre' => $this->nombre,
            'razon_social' => $this->razon_social,
            'ruc' => $this->ruc,
            'direccion' => $this->direccion,
            'telefono' => $this->telefono,
            'correo' => $this->correo,
            'sitio_web' => $this->sitio_web,
            'estado' => $this->estado,
            'creado_por' => Auth::id(),
        ];

        // Handle logo upload - pass the file object, service will handle it
        if ($this->logo) {
            $data['logo'] = $this->logo;
        }

        return EmpresaData::fromArray($data);
    }

    public function updatedLogo(): void
    {
        $this->validate([
            'logo' => 'image|max:2048'
        ]);

        $this->logo_preview = $this->logo->temporaryUrl();
    }

    public function removeLogo(): void
    {
        $this->logo = null;
        $this->logo_preview = null;
    }

    public function resetForm(): void
    {
        $this->reset(['nombre', 'razon_social', 'ruc', 'direccion', 
            'telefono', 'correo', 'sitio_web', 'estado', 'logo', 'logo_preview']);
        $this->isEditing = false;
        $this->empresa = null;
        $this->resetValidation();
    }

    public function cancel(): void
    {
        $this->resetForm();
        $this->dispatch('close-modal', name: 'empresa-form-modal');
    }

    #[On('edit-empresa')]
    public function edit(int $empresaId): void
    {
        $this->resetForm();
        $this->empresa = Empresa::find($empresaId);
        
        if ($this->empresa) {
            $this->isEditing = true;
            $this->fillFields($this->empresa);
            $this->dispatch('open-modal', name: 'empresa-form-modal');
        } else {
            $this->dispatch('toast', message: 'Empresa no encontrada', type: 'error');
        }
    }

    #[On('create-empresa')]
    public function create(): void
    {
        $this->resetForm();
        $this->isEditing = false;
        $this->dispatch('open-modal', name: 'empresa-form-modal');
    }

    public function render()
    {
        return view('livewire.empresas.empresa-form');
    }
}