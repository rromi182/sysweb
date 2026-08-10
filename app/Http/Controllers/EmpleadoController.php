<?php

namespace App\Http\Controllers;

use App\DTOs\EmpleadoData;
use App\Exceptions\EmpleadoException;
use App\Http\Requests\StoreEmpleadoRequest;
use App\Http\Requests\UpdateEmpleadoRequest;
use App\Models\Empleado;
use App\Models\Cargo;
use App\Models\Departamento;
use App\Models\Empresa;
use App\Models\HorarioLaboral;
use App\Models\Sucursal;
use App\Models\TipoContrato;
use App\Services\EmpleadoService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class EmpleadoController extends Controller
{
    public function __construct(
        private readonly EmpleadoService $service
    ) {}

    /**
     * Página de listado (vista contenedora de Livewire).
     */
    public function index()
    {
        return view('empleados.index');
    }

    /**
     * Formulario de creación (vista Blade tradicional).
     */
    public function create()
    {
        return view('empleados.create', [
            'empleado' => new Empleado(),
            'empresas' => Empresa::where('estado', 1)->orderBy('nombre')->get(),
            'sucursales' => collect(),
            'departamentos' => collect(),
            'cargos' => collect(),
            'tiposContrato' => collect(),
            'horarios' => collect(),
            'jefes' => Empleado::with('persona')->activo()->get(),
            'nextCode' => $this->service->generateNextCode(),
        ]);
    }

    /**
     * Guardar nuevo empleado.
     */
    public function store(StoreEmpleadoRequest $request)
    {
        try {
            $data = EmpleadoData::fromArray($request->validated(), Auth::id());

            $empleado = $this->service->createEmpleado($data);

            return redirect()->route('empleados.index')
                ->with('success', 'Empleado creado exitosamente.');

        } catch (EmpleadoException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error creando empleado: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error al crear el empleado.');
        }
    }

    /**
     * Detalle de empleado.
     */
    public function show(Empleado $empleado)
    {
        $empleado->load([
            'persona', 'empresa', 'sucursal', 'departamento',
            'cargo', 'tipoContrato', 'horario', 'jefeInmediato.persona',
            'creador', 'actualizador',
        ]);

        return view('empleados.show', compact('empleado'));
    }

    /**
     * Formulario de edición.
     */
    public function edit(Empleado $empleado)
    {
        $empleado->load('persona');

        return view('empleados.edit', [
            'empleado' => $empleado,
            'empresas' => Empresa::where('estado', 1)->orderBy('nombre')->get(),
            'sucursales' => Sucursal::where('empresa_id', $empleado->empresa_id)
                ->where('estado', 1)->orderBy('nombre')->get(),
            'departamentos' => Departamento::where('empresa_id', $empleado->empresa_id)
                ->where('estado', 1)->orderBy('nombre')->get(),
            'cargos' => Cargo::where('empresa_id', $empleado->empresa_id)
                ->where('estado', 1)->orderBy('nombre')->get(),
            'tiposContrato' => TipoContrato::where('empresa_id', $empleado->empresa_id)
                ->orderBy('nombre')->get(),
            'horarios' => HorarioLaboral::where('empresa_id', $empleado->empresa_id)
                ->where('estado', 1)->orderBy('nombre')->get(),
            'jefes' => Empleado::with('persona')
                ->where('id', '!=', $empleado->id)
                ->activo()
                ->get(),
        ]);
    }

    /**
     * Actualizar empleado.
     */
    public function update(UpdateEmpleadoRequest $request, Empleado $empleado)
    {
        try {
            $data = EmpleadoData::fromArray($request->validated(), Auth::id());

            $this->service->updateEmpleado($empleado, $data);

            return redirect()->route('empleados.index')
                ->with('success', 'Empleado actualizado exitosamente.');

        } catch (EmpleadoException $e) {
            return back()->withInput()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            Log::error('Error actualizando empleado: ' . $e->getMessage());
            return back()->withInput()->with('error', 'Error al actualizar el empleado.');
        }
    }

    /**
     * Eliminar empleado.
     */
    public function destroy(Empleado $empleado)
    {
        try {
            $this->service->deleteEmpleado($empleado);

            return redirect()->route('empleados.index')
                ->with('success', 'Empleado eliminado exitosamente.');

        } catch (EmpleadoException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Error al eliminar el empleado.');
        }
    }

    /**
     * Inactivar empleado.
     */
    public function inactivar(Empleado $empleado)
    {
        try {
            $this->service->inactivarEmpleado($empleado);

            return back()->with('success', 'Empleado inactivado correctamente.');

        } catch (EmpleadoException $e) {
            return back()->with('error', $e->getMessage());
        } catch (\Exception $e) {
            return back()->with('error', 'Error al inactivar el empleado.');
        }
    }

    /**
     * Buscar empleados (JSON para autocomplete, select2, TomSelect, etc.).
     */
    public function search(Request $request): JsonResponse
    {
        $query = $request->input('q') ?? $request->input('search');

        $results = $this->service->searchEmpleados(
            query: $query,
            activeOnly: $request->boolean('active_only', true),
            limit: $request->integer('limit', 50),
        );

        return response()->json($results);
    }
}