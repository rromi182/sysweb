<?php

namespace App\Livewire\Nominas;

use Livewire\Component;
use App\Models\Empleado;
use App\Models\Empresa;
use App\Models\MovimientoNomina;
use App\Enums\TipoMovimientoEnum;
use App\DTOs\MovimientoNominaDTO;
use App\Services\NominaService;
use Illuminate\Support\Facades\Auth;

class NominaForm extends Component
{
    public $empleados = [];
    public $empleado_id = '';
    public $buscarEmpleado = '';
    public $mostrarDropdown = false;
    public $fecha;
    public $tipo_movimiento = 'sueldo';
    public $monto = 0;
    public $observacion = '';
    public $anio;
    public $mes;
    public $movimientoId = null;
    public $modoEdicion = false;

    protected $listeners = ['editarMovimiento', 'resetForm'];

    public function mount()
    {
        $this->anio = now()->year;
        $this->mes = now()->month;
        $this->fecha = now()->format('Y-m-d');
        $this->empleados = Empleado::with('persona')->get();
        $this->cargarEmpleados();
    }

    // 🔥 NUEVO: Método genérico para detectar cambios en cualquier propiedad
    public function updated($propertyName)
    {
        // Cuando cambia el tipo de movimiento
        if ($propertyName === 'tipo_movimiento') {
            if ($this->tipo_movimiento === 'extra') {
                $this->monto = 500000;
            } else {
                $this->monto = 0;
            }
        }

        // Cuando cambia la búsqueda de empleado
        if ($propertyName === 'buscarEmpleado') {
            $this->cargarEmpleados();
        }
    }

    // 🔥 MÉTODO ALTERNATIVO: Si prefieres nombres específicos
    // public function updatingTipoMovimiento($value)
    // {
    //     if ($value === 'extra') {
    //         $this->monto = 500000;
    //     } else {
    //         $this->monto = 0;
    //     }
    // }

    public function editarMovimiento($id)
    {
        $mov = MovimientoNomina::findOrFail($id);
        $this->movimientoId = $mov->id;
        $this->empleado_id = $mov->empleado_id;
        $this->fecha = $mov->fecha->format('Y-m-d');
        $this->tipo_movimiento = $mov->tipo_movimiento->value;
        $this->monto = $mov->monto;
        $this->observacion = $mov->observacion ?? '';
        $this->anio = $mov->anio;
        $this->mes = $mov->mes;
        $this->modoEdicion = true;
    }

    public function resetForm()
    {
        $this->reset(['empleado_id', 'tipo_movimiento', 'monto', 'observacion', 'movimientoId', 'modoEdicion']);
        $this->tipo_movimiento = 'sueldo';
        $this->fecha = now()->format('Y-m-d');
    }

public function save(NominaService $service)
{
    // 🔥 CONVIERTE EL MONTO EXPLÍCITAMENTE ANTES DE VALIDAR
    $this->monto = (float) $this->monto;

    $this->validate([
        'empleado_id' => 'required|exists:empleados,id',
        'fecha' => 'required|date',
        'tipo_movimiento' => 'required',
        'monto' => 'required|numeric|min:0',
        'observacion' => 'required_if:tipo_movimiento,otros',
        'anio' => 'required|integer',
        'mes' => 'required|integer|min:1|max:12',
    ], [
        'observacion.required_if' => 'La observación es obligatoria para movimientos tipo OTROS.',
    ]);

    if (!Auth::check()) {
        session()->flash('error', 'Usuario no autenticado');
        return;
    }

    $user = Auth::user();

    // 👇 OBTENER EMPRESA DE FORMA SEGURA
    $empresaId = null;

    if (method_exists($user, 'empresas') && $user->empresas()->exists()) {
        $empresaId = $user->empresas()->first()->id;
    }

    if (!$empresaId && $this->empleado_id) {
        $empleado = Empleado::with('empresa')->find($this->empleado_id);
        if ($empleado && $empleado->empresa_id) {
            $empresaId = $empleado->empresa_id;
        }
    }

    if (!$empresaId) {
        $empresaId = Empresa::first()->id ?? null;
    }

    if (!$empresaId) {
        session()->flash('error', 'No se pudo determinar la empresa.');
        return;
    }

    // 🔥 DEPURACIÓN EXTREMA
    \Log::info('========== VALOR DEL MONTO ==========');
    \Log::info('monto_raw: ' . $this->monto);
    \Log::info('monto_tipo: ' . gettype($this->monto));
    \Log::info('monto_cast: ' . (float) $this->monto);
    \Log::info('=====================================');

    $dto = MovimientoNominaDTO::fromRequest(
        [
            'empleado_id' => $this->empleado_id,
            'fecha' => $this->fecha,
            'tipo_movimiento' => $this->tipo_movimiento,
            'monto' => (float) $this->monto,
            'observacion' => $this->observacion,
            'anio' => $this->anio,
            'mes' => $this->mes,
        ],
        $empresaId,
        Auth::id()
    );

    // 🔥 DEPURACIÓN DEL DTO
    \Log::info('========== DTO CREADO ==========');
    \Log::info('DTO monto: ' . $dto->monto);
    \Log::info('DTO monto tipo: ' . gettype($dto->monto));
    \Log::info('DTO array: ' . json_encode($dto->toArray()));
    \Log::info('================================');

    if ($this->modoEdicion && $this->movimientoId) {
        $mov = MovimientoNomina::find($this->movimientoId);
        $mov->update($dto->toArray());
        $message = 'Movimiento actualizado correctamente';
    } else {
        $service->registrarMovimiento($dto);
        $message = 'Movimiento registrado correctamente';
    }

    $this->dispatch('movimientoGuardado');
    $this->resetForm();

    session()->flash('message', $message);
}

    public function cargarEmpleados()
    {
        $query = Empleado::with('persona')->where('estado', 'activo');

        if (!empty($this->buscarEmpleado)) {
            $query->whereHas('persona', function ($q) {
                $q->where('nombres', 'LIKE', '%' . $this->buscarEmpleado . '%')
                    ->orWhere('apellidos', 'LIKE', '%' . $this->buscarEmpleado . '%')
                    ->orWhere('numero_documento', 'LIKE', '%' . $this->buscarEmpleado . '%');
            });
        }

        $this->empleados = $query->limit(20)->get();
        $this->mostrarDropdown = true;
    }

    public function seleccionarEmpleado($id)
    {
        $empleado = Empleado::with('persona')->find($id);
        if ($empleado) {
            $this->empleado_id = $id;
            $this->buscarEmpleado = $empleado->persona->nombres . ' ' . $empleado->persona->apellidos . ' (' . $empleado->persona->numero_documento . ')';
            $this->mostrarDropdown = false;
        }
    }

    public function ocultarDropdown()
    {
        $this->mostrarDropdown = false;
    }

    public function render()
    {
        return view('livewire.nominas.nomina-form', [
            'tipos' => TipoMovimientoEnum::cases(),
        ]);
    }
}
