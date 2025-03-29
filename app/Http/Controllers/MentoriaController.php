<?php

namespace App\Http\Controllers;

use App\Events\MatriculaAluno;
use Symfony\Component\HttpFoundation\Response;
use App\Models\Mentoria;
use App\Models\Mentor;
use App\Http\Requests\UpdateMentoriaRequest;
use App\Http\Requests\StoreMentoriaRequest;

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
        $data = $request->all();
        $existente = Mentoria::where('user_id', $data['user_id'])->where('mentor_id', $data['mentor_id'])->where('ativa', true)->get()->first();
        if($existente){
            return response()->json(['success' => false, 'message' => 'Usuário já possui mentoria ativa deste mentor']);
        }
        $mentoria = Mentoria::create($data);
        $mentor = Mentor::withCount('mentorias')->find($data['mentor_id']);
        broadcast(new MatriculaAluno($mentor->mentorias_count, $mentor->id));
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
