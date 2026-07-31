<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use App\Services\EmpresaService;
use App\DTOs\EmpresaData;
use App\Http\Requests\EmpresaRequest;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function __construct(
        protected EmpresaService $empresaService
    ) {}

    public function index(Request $request)
    {
        $empresas = $this->empresaService->getAllEmpresas(
            $request->boolean('activos')
        );

        return response()->json([
            'success' => true,
            'data' => $empresas,
        ]);
    }

    public function store(EmpresaRequest $request)
    {
        // Crear DTO desde la request
        $data = EmpresaData::fromArray($request->validated());

        // Si hay logo, subirlo y actualizar el DTO
        if ($request->hasFile('logo')) {
            $logo = $this->empresaService->uploadLogo($request->file('logo'));
            $data = EmpresaData::fromArray(array_merge(
                $request->validated(),
                ['logo' => $logo]
            ));
        }

        $empresa = $this->empresaService->createEmpresa($data);

        return response()->json([
            'success' => true,
            'message' => 'Empresa creada exitosamente',
            'data' => $empresa,
        ], 201);
    }

    public function show(Empresa $empresa)
    {
        $empresa->load(['sucursales', 'empleados', 'cargos', 'departamentos']);

        return response()->json([
            'success' => true,
            'data' => $empresa,
        ]);
    }

    public function update(EmpresaRequest $request, Empresa $empresa)
    {
        // Crear DTO desde la request
        $data = EmpresaData::fromArray($request->validated());

        // Si hay logo, subirlo y actualizar el DTO
        if ($request->hasFile('logo')) {
            $logo = $this->empresaService->uploadLogo(
                $request->file('logo'),
                $empresa->logo
            );
            $data = EmpresaData::fromArray(array_merge(
                $request->validated(),
                ['logo' => $logo]
            ));
        }

        $empresa = $this->empresaService->updateEmpresa($empresa, $data);

        return response()->json([
            'success' => true,
            'message' => 'Empresa actualizada exitosamente',
            'data' => $empresa,
        ]);
    }

    public function destroy(Empresa $empresa)
    {
        $this->empresaService->deleteEmpresa($empresa);

        return response()->json([
            'success' => true,
            'message' => 'Empresa eliminada exitosamente',
        ]);
    }
}