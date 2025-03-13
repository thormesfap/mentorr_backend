<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\Empresa;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CargoController extends Controller
{
    /**
     * Listar Cargos
     *
     * Lista todos os cargos cadastrados
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $perPage = $request->query('per_page', Cargo::PER_PAGE);
        if($request->get('search')){
            return response()->json(Cargo::where('nome', 'like', '%'.$request->get('search').'%')->paginate($perPage), Response::HTTP_OK);
        }
        return response()->json(Cargo::paginate($perPage), Response::HTTP_OK);
    }

    /**
     * Criar Cargo
     *
     * Cria um novo Cargo
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $valid = $request->validate([
            'nome' => 'required',
        ]);
        $cargo = new Cargo();
        $cargo->nome = $valid['nome'];
        $cargo->save();
        return response()->json($cargo, Response::HTTP_CREATED);
    }

    /**
     * Mostrar Cargo
     *
     * Mostra o cargo especificado
     */
    public function show(Cargo $cargo): \Illuminate\Http\JsonResponse
    {
        return response()->json($cargo, Response::HTTP_OK);
    }

    /**
     * Atualiza Cargo
     *
     * Atualiza nome do Cargo especificado
     */
    public function update(Request $request, Cargo $cargo): \Illuminate\Http\JsonResponse
    {
        $valid = $request->validate([
            'nome' => 'required',
        ]);
        $valid->nome = $valid['nome'];
        $cargo->save();
        return response()->json($cargo, Response::HTTP_OK);
    }

    /**
     * Remover Cargo
     *
     * Remove o cargo especificado
     */
    public function destroy(Cargo $cargo): \Illuminate\Http\JsonResponse
    {
        $cargo->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
