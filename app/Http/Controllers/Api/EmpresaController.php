<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Empresa;
use Illuminate\Http\Request;

class EmpresaController extends Controller
{
    public function search(Request $request)
    {
        $search = $request->input('q', '');
        
        $empresas = Empresa::query()
            ->when($search, function ($query, $search) {
                return $query->where('nombre', 'LIKE', "%{$search}%")
                    ->orWhere('razon_social', 'LIKE', "%{$search}%")
                    ->orWhere('ruc', 'LIKE', "%{$search}%");
            })
            ->limit(10)
            ->get()
            ->map(function ($empresa) {
                return [
                    'id' => $empresa->id,
                    'nombre' => $empresa->nombre,
                    'razon_social' => $empresa->razon_social,
                    'ruc' => $empresa->ruc,
                ];
            });

        return response()->json($empresas);
    }
}