<?php

namespace App\Http\Controllers;

use App\Models\Mentoria;
use App\Http\Requests\StoreMentoriaRequest;
use App\Http\Requests\UpdateMentoriaRequest;
use Symfony\Component\HttpFoundation\Response;

class MentoriaController extends Controller
{
    /**
     * Listar Mentorias
     *
     * Mostra todas as mentorias.
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        return response()->json(Mentoria::all(), Response::HTTP_OK);
    }

    /**
     * Criar Mentoria
     *
     * Cria uma nova mentoria
     */
    public function store(StoreMentoriaRequest $request): \Illuminate\Http\JsonResponse
    {
        $mentoria = new Mentoria();
        $mentoria->fill($request->all());
        $mentoria->save();
        return response()->json($mentoria, Response::HTTP_CREATED);
    }

    /**
     * Mostra Mentoria
     *
     * Mostra mentoria do id especificado
     */
    public function show(Mentoria $mentoria): \Illuminate\Http\JsonResponse
    {
        return response()->json($mentoria, Response::HTTP_OK);
    }


    /**
     * Atualiza Mentoria
     *
     * Atualiza dados da mentoria especificada
     */
    public function update(UpdateMentoriaRequest $request, Mentoria $mentoria): \Illuminate\Http\JsonResponse
    {
        $mentoria->fill($request->all());
        $mentoria->save();
        return response()->json($mentoria, Response::HTTP_OK);
    }

    /**
     * Remove Mentoria
     *
     * Remove a mentoria do banco de dados
     */
    public function destroy(Mentoria $mentoria): \Illuminate\Http\JsonResponse
    {
        $mentoria->delete();
        return response()->json($mentoria, Response::HTTP_NO_CONTENT);
    }

    /**
     * Minhas Mentorias
     *
     * Lista as mentorias do usuário logado
     */
    public function minhasMentorias(): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        return response()->json(Mentoria::where('user_id', $user->id)->get(), Response::HTTP_OK);
    }
}
