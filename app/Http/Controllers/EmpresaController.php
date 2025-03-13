<?php

namespace App\Http\Controllers;

use App\Models\Empresa;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EmpresaController extends Controller
{
    /**
     *
     * Lista de Empresas
     *
     * Traz a lista de todas as empresas cadastradas
     */
    public function index(Request $request)
    {
        $perPage = $request->query('per_page', Empresa::PER_PAGE);
        if($request->get('search')){
            return response()->json(Empresa::where('nome', 'like', '%'.$request->get('search').'%')->paginate($perPage), Response::HTTP_OK);
        }
        return response()->json(Empresa::paginate($perPage), Response::HTTP_OK);
    }

    /**
     * Cria Empresa
     *
     * Endpoint para receber dados e criar empresa
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
     * Mostrar Empresa
     *
     * Mostra a empresa do id informado
     *
     */
    public function show(Empresa $empresa)
    {
        return response()->json($empresa, Response::HTTP_OK);
    }


    /**
     * Atualiza Empresa
     *
     * Atualiza os dados da empresa com o id informado
     *
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
     * Remove Empresa
     *
     * Remove a empresa com o id informado
     */
    public function destroy(Empresa $empresa)
    {
        $empresa->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
