<?php

namespace App\Http\Controllers;

use App\Models\Area;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class AreaController extends Controller
{
    /**
     * Lista Áreas
     *
     * Lista todas as áreas cadastradas
     */
    public function index(Request $request)
    {
        if($request->get('search')){
            return response()->json(Area::where('nome', 'like', '%'.$request->get('search').'%')->paginate(), Response::HTTP_OK);
        }
        return response()->json(Area::paginate(),Response::HTTP_OK);
    }

    /**
     * Criar Área
     *
     * Cria uma nova Área
     */
    public function store(Request $request)
    {
        $valid = $request->validate([
            'nome' => 'required',
        ]);
        $area = new Area();
        $area->nome = $valid['nome'];
        $area->save();
        return response()->json($area,Response::HTTP_CREATED);
    }

    /**
     * Mostrar Área
     *
     * Mostra a área do id especificado
     */
    public function show(Area $area)
    {
        return response()->json($area, Response::HTTP_OK);
    }

    /**
     * Atualiza Área
     *
     * Atualiza dados da área especificada
     */
    public function update(Request $request, Area $area)
    {
        $valid = $request->validate([
            'nome' => 'required',
        ]);
        $area->nome = $valid['nome'];
        $area->save();
        return response()->json($area,Response::HTTP_OK);
    }

    /**
     * Remove Área
     *
     * Remove a Área com o id especificado
     */
    public function destroy(Area $area)
    {
        $area->delete();
        return response()->json(null,Response::HTTP_NO_CONTENT);
    }
}
