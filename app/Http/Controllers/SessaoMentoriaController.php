<?php

namespace App\Http\Controllers;

use App\Models\Mentoria;
use App\Models\SessaoMentoria;
use Carbon\Carbon;
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
        return response()->json(SessaoMentoria::all(), Response::HTTP_OK);
    }

    /**
     * Criar Sessão
     *
     * Cria sessão de mentoria
     */
    public function store(Request $request): \Illuminate\Http\JsonResponse
    {
        $valid = $request->validate([
            'data_hora_inicio' => 'required|date_format:d/m/Y H:i',
            'mentoria_id' => 'required',
        ]);

        $user = auth('api')->user();
        if (!$user->mentor->id) {
            return response()->json(['success' => false, 'message' => 'Apenas Mentores podem agendar sessão'], Response::HTTP_FORBIDDEN);
        }
        $mentoria = Mentoria::findOrFail($valid['mentoria_id']);
        if (!$mentoria->ativa) {
            return response()->json(['message' => 'Mentoria já foi encerrada'], Response::HTTP_BAD_REQUEST);
        }

        $inicio = Carbon::createFromFormat("d/m/Y H:i", $valid['data_hora_inicio']);
        foreach ($mentoria->sessoes as $existente) {
            $inicioExistente = Carbon::parse($existente->data_hora_inicio);
            $terminoExistente = Carbon::parse($existente->data_hora_termino);
            if ($inicio >= $inicioExistente && $inicio <= $terminoExistente) {
                return response()->json(["success" => false,'message' => 'Já há sessão de mentoria no período indicado'], Response::HTTP_FORBIDDEN);
            }
        }
        if ($mentoria->mentor->id != $user->mentor->id) {
            return response()->json(["success" => false,'message' => 'Mentoria não pertence ao mentor'], Response::HTTP_FORBIDDEN);
        }

        $sessoes = count($mentoria->sessoes);
        if($sessoes == 0){
            $mentoria->data_hora_inicio = $inicio;
        }
        if ($sessoes > $user->mentor->quantidade_chamadas) {
            $mentoria->ativa = false;
            $mentoria->save();
            return response()->json(["success" => false,'message' => 'Já foram esgotadas as sessões dessa mentoria'], Response::HTTP_BAD_REQUEST);
        }

        $tempo = $user->mentor->minutos_por_chamada;

        $sessaoMentoria = new SessaoMentoria();
        $sessaoMentoria->fill(['mentoria_id' => $valid['mentoria_id']]);
        $sessaoMentoria->data_hora_inicio = $inicio->format("Y-m-d H:i:s");
        $fim = $inicio->addMinutes($tempo);
        $sessaoMentoria->data_hora_termino = $fim->format("Y-m-d H:i:s");

        $sessaoMentoria->save();
        if ($sessoes + 1 == $user->mentor->quantidade_chamadas) {
            $mentoria->ativa = false;
            $mentoria->data_hora_termino = $sessaoMentoria->data_hora_termino;
            $mentoria->save();
        }
        return response()->json($sessaoMentoria, Response::HTTP_CREATED);
    }

    /**
     * Mostra Sessão
     *
     * Apresentada uma sessão de mentoria específica
     */
    public function show(SessaoMentoria $sessaoMentoria): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        if ($sessaoMentoria->mentoria->usuario->id != $user->id) {
            return response()->json(['message' => 'Mentoria não pertence ao usuário'], Response::HTTP_FORBIDDEN);
        }
        return response()->json($sessaoMentoria, Response::HTTP_OK);
    }

    /**
     * Atualiza Sessão
     *
     * Atualiza dados da sessão de mentoria especificada
     */
    public function update(Request $request, SessaoMentoria $sessaoMentoria): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        if ($sessaoMentoria->mentoria->usuario->id != $user->id) {
            return response()->json(['message' => 'Mentoria não pertence ao usuário'], Response::HTTP_FORBIDDEN);
        }
        $valid = $request->validate([
            'data_hora_inicio' => 'required|date_format:H:i',
            'data_hora_termino' => 'required|date_format:H:i',
        ]);
        $sessaoMentoria->fill($valid);
        $sessaoMentoria->save();
        return response()->json($sessaoMentoria, Response::HTTP_OK);
    }

    /**
     * Avaliar Sessão
     *
     * Endpoint para realizar avaliação da sessão de mentoria
     */
        public function avaliar(Request $request, int $idSessaoMentoria): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        $sessao = SessaoMentoria::with(['mentoria'])->where('id', $idSessaoMentoria)->get()->first();
        if ($sessao->mentoria->user_id != $user->id) {
            return response()->json(['message' => 'Mentoria não pertence ao usuário'], Response::HTTP_FORBIDDEN);
        }

        $valid = $request->validate(['avaliacao' => 'required|numeric|max:5|min:0']);
        $sessao->avaliacao = round($valid['avaliacao'], 1);
        $sessao->save();
        return response()->json($sessao, Response::HTTP_OK);
    }

    /**
     * Remove Sessão
     *
     * Apagar a sessão de mentoria
     */
    public function destroy(SessaoMentoria $sessaoMentoria): \Illuminate\Http\JsonResponse
    {
        $sessaoMentoria->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
