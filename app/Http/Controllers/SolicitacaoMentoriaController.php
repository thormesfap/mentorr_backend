<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use App\Models\User;
use App\Models\SolicitacaoMentoria;
use App\Models\Mentoria;
use App\Models\Mentor;
use App\Mail\RespostaSolicitacaoMentoria;
use App\Mail\NotificacaoSolicitacaoMentoria;
use App\Jobs\SendEmail;
use App\Http\Requests\UpdateSolicitacaoMentoriaRequest;
use App\Http\Requests\StoreSolicitacaoMentoriaRequest;
use App\Events\MatriculaAluno;
use App\Events\MentoriaRespondida;

class SolicitacaoMentoriaController extends Controller
{
    /**
     * Store a newly created resource in storage.
     */
    public function store(StoreSolicitacaoMentoriaRequest $request): JsonResponse
    {
        $user = auth('api')->user();
        $expectativa = $request->expectativa;
        if ($user->mentor && $user->mentor->id == $request->mentor_id) {
            return response()->json(['sucess' => false, 'message' => 'Não pode solicitar mentoria para si mesmo'], Response::HTTP_BAD_REQUEST);
        }

        $mentor = Mentor::findOrFail($request->mentor_id);

        $pendente = SolicitacaoMentoria::where('user_id', $user->id)->where('mentor_id', $request->mentor_id)->whereNull('data_hora_resposta')->get();
        if ($pendente->count() > 0) {
            return response()->json(['sucess' => false, 'message' => 'Já há solicitação pendente para este mentor, aguarde resposta'], Response::HTTP_BAD_REQUEST);
        }

        $solicitacao = SolicitacaoMentoria::create([
            'user_id' => $user->id,
            'mentor_id' => $mentor->id,
            'expectativa' => $expectativa
        ]);


        // Dispara o evento de matricula
        broadcast(new MatriculaAluno($mentor->mentorias_count + 1, $mentor->id));

        // Envia e-mail de notificação para o mentor
        SendEmail::dispatch(
            $mentor->user->email,
            new NotificacaoSolicitacaoMentoria($mentor, $user)
        );
        $solicitacao->mentor = $mentor;
        $solicitacao->user = $user;

        return response()->json($solicitacao, 201);
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(UpdateSolicitacaoMentoriaRequest $request, SolicitacaoMentoria $solicitacao): JsonResponse
    {
        $user = auth('api')->user();
        if ($solicitacao->mentor_id !== $user->mentor->id) {
            return response()->json(['message' => 'Não autorizado'], 403);
        }

        if (!is_null($solicitacao->data_hora_resposta)) {
            return response()->json(['message' => 'Esta solicitação já foi respondida'], 422);
        }

        $solicitacao->update([
            'aceita' => $request->aceita,
            'justificativa' => $request->justificativa,
            'data_hora_resposta' => now()
        ]);
        $solicitacao->save();

        $aluno = $solicitacao->user;
        $mentor = $solicitacao->mentor;

        // Envia e-mail de resposta para o aluno
        SendEmail::dispatch(
            $aluno->email,
            new RespostaSolicitacaoMentoria(
                $mentor,
                $aluno,
                $request->aceita,
                $request->justificativa
            )
        );

        // Se a mentoria foi aceita, dispara o evento e cria uma nova mentoria
        if ($request->aceita) {
            Mentoria::create([
                'user_id' => $solicitacao->user_id,
                'mentor_id' => $solicitacao->mentor_id,
                'expectativa' => $solicitacao->expectativa,
                'valor' => $mentor->preco,
                'quantidade_sessoes' => $mentor->quantidade_chamadas
            ]);
        }
        broadcast(new MentoriaRespondida($mentor->id, $aluno->id));

        return response()->json($solicitacao);
    }

    /**
     * Lista todas as solicitações de mentoria recebidas pelo mentor.
     */
    public function listarPorMentor(): JsonResponse
    {
        $user = auth('api')->user();
        $solicitacoes = SolicitacaoMentoria::with(['user', 'mentor'])
            ->where('mentor_id', $user->mentor->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($solicitacoes);
    }

    /**
     * Lista todas as solicitações de mentoria feitas pelo usuário.
     */
    public function listarPorUsuario(): JsonResponse
    {
        $user = auth('api')->user();
        $solicitacoes = SolicitacaoMentoria::with(['user', 'mentor'])
            ->where('user_id', $user->id)
            ->orderBy('created_at', 'desc')
            ->get();

        return response()->json($solicitacoes);
    }
}
