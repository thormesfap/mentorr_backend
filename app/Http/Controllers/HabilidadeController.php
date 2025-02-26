<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Habilidade;
use App\Models\Mentor;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class HabilidadeController extends Controller
{
    /**
     * Listar Habilidades
     *
     * Lista todas as habilidades cadastradas
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        if($request->get('search')){
            return response()->json(Habilidade::where('nome', 'like', '%'.$request->get('search').'%')->paginate(), Response::HTTP_OK);
        }
        return response()->json(Habilidade::paginate(5), Response::HTTP_OK);
    }

    /**
     * Criar Habilidade
     *
     * Cria uma habilidade, informando a área
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $valid = $request->validate([
            'nome' => 'required',
            'area_id' => 'required'
        ]);
        $area = Area::findOrFail($valid['area_id']);
        $habilidade = new Habilidade();
        $habilidade->fill($valid);
        $habilidade->save();
        return response()->json($habilidade, Response::HTTP_CREATED);
    }

    /**
     * Mostra Habilidade
     *
     * Apresenta dados da habilidade especificada
     */
    public function show(Habilidade $habilidade): \Illuminate\Http\JsonResponse
    {
        return response()->json($habilidade, Response::HTTP_OK);
    }

    /**
     * Atualiza Habilidade
     *
     * Atualiza dados da habilidade especificada
     */
    public function update(Request $request, Habilidade $habilidade): \Illuminate\Http\JsonResponse
    {
        $valid = $request->validate([
            'nome' => 'required',
            'area_id' => 'required'
        ]);
        $habilidade->fill($valid);
        $habilidade->save();
        return response()->json($habilidade, Response::HTTP_OK);
    }

    /**
     * Remove Habilidade
     *
     * Remove a habilidade especificada
     */
    public function destroy(Habilidade $habilidade): \Illuminate\Http\JsonResponse
    {
        $habilidade->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }

    /**
     * Habilidades do Mentor
     *
     * Lista as habilidades cadastradas para o mentor com a id especificada
     */
    public function doMentor(int $idMentor): \Illuminate\Http\JsonResponse
    {
        $mentor = Mentor::with('habilidades')->findOrFail($idMentor);
        return response()->json($mentor->habilidades, Response::HTTP_OK);
    }
}
