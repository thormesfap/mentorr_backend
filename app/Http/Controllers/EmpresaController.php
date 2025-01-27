<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmpresaController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        return response()->json(Empresa::all(), Response::HTTP_OK);
    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(Request $request)
    {
        $valid = $request->validate([
            'nome' => 'required',
        ]);
        $empresa = new Empresa();
        $empresa->nome = $valid['nome'];
        $empresa->save();
        return response()->json($empresa, Response::HTTP_CREATED);
    }

    /**
     * Display the specified resource.
     */
    public function show(Empresa $empresa)
    {
        return response()->json($empresa, Response::HTTP_OK);
    }


    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, Empresa $empresa)
    {
        $valid = $request->validate([
            'nome' => 'required',
        ]);
        $empresa->nome = $valid['nome'];
        $empresa->save();
        return response()->json($empresa, Response::HTTP_OK);
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(Empresa $empresa)
    {
        $empresa->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
