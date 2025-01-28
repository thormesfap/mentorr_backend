<?php

namespace App\Http\Controllers;

use App\Models\Mentoria;
use App\Models\SessaoMentoria;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class SessaoMentoriaController extends Controller
{
    /**
     * Listar Sessões
     *
     * Lista todas as sessões de mentoria do sistema
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
       return response()->json(SessaoMentoria::all(),Response::HTTP_OK);
    }

    /**
     * Criar Sessão
     *
     * Cria sessão de mentoria
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $valid = $request->validate([
            'data_hora_inicio' => 'required|date_format:H:i',
            'data_hora_termino' => 'required|date_format:H:i',
            'mentoria_id' => 'required',
        ]);
        $user = auth('api')->user();
        $mentoria = Mentoria::findOrFail($valid['mentoria_id']);
        if($mentoria->usuario->id != $user->id){
            return response()->json(['message' => 'Mentoria não pertence ao usuário'], Response::HTTP_FORBIDDEN);
        }
        $sessaoMentoria = new SessaoMentoria();
        $sessaoMentoria->fill($valid);
        $sessaoMentoria->save();
        return response()->json($sessaoMentoria,Response::HTTP_CREATED);
    }

    /**
     * Mostra Sessão
     *
     * Apresentada uma sessão de mentoria específica
     */
    public function show(SessaoMentoria $sessaoMentoria): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        if($sessaoMentoria->mentoria->usuario->id != $user->id){
            return response()->json(['message' => 'Mentoria não pertence ao usuário'], Response::HTTP_FORBIDDEN);
        }
        return response()->json($sessaoMentoria,Response::HTTP_OK);
    }

    /**
     * Atualiza Sessão
     *
     * Atualiza dados da sessão de mentoria especificada
     */
    public function update(Request $request, SessaoMentoria $sessaoMentoria): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        if($sessaoMentoria->mentoria->usuario->id != $user->id){
            return response()->json(['message' => 'Mentoria não pertence ao usuário'], Response::HTTP_FORBIDDEN);
        }
        $valid = $request->validate([
            'data_hora_inicio' => 'required|date_format:H:i',
            'data_hora_termino' => 'required|date_format:H:i',
        ]);
        $sessaoMentoria->fill($valid);
        $sessaoMentoria->save();
        return response()->json($sessaoMentoria,Response::HTTP_OK);
    }

    /**
     * Avaliar Sessão
     *
     * Endpoint para realizar avaliação da sessão de mentoria
     */
    public function avaliar(Request $request, SessaoMentoria $sessaoMentoria): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        if($sessaoMentoria->mentoria->usuario->id != $user->id){
            return response()->json(['message' => 'Mentoria não pertence ao usuário'], Response::HTTP_FORBIDDEN);
        }
        $valid = $request->validate(['avaliacao' => 'required|numeric|max:5|min:0']);
        $sessaoMentoria->avaliacao = round($valid['avaliacao'], 1);
        $sessaoMentoria->save();
        return response()->json($sessaoMentoria,Response::HTTP_OK);
    }

    /**
     * Remove Sessão
     *
     * Apagar a sessão de mentoria
     */
    public function destroy(SessaoMentoria $sessaoMentoria): \Illuminate\Http\JsonResponse
    {
        $sessaoMentoria->delete();
        return response()->json(null,Response::HTTP_NO_CONTENT);
    }
}
