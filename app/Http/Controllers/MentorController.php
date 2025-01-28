<?php

namespace App\Http\Controllers;

use App\Models\Cargo;
use App\Models\Empresa;
use App\Models\Habilidade;
use App\Models\Mentor;
use App\Http\Requests\StoreMentorRequest;
use App\Http\Requests\UpdateMentorRequest;
use App\Models\Mentoria;
use Illuminate\Support\Facades\Request;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class MentorController extends Controller
{
    /**
     * Listar Mentores
     *
     * Lista todos os mentores cadastrados
     */
    public function index(): \Illuminate\Http\JsonResponse
    {
        return response()->json(Mentor::all(), Response::HTTP_OK);
    }

    /**
     * Registrar Mentor
     *
     * Realiza cadastro do usuário como Mentor
     */
    public function store(StoreMentorRequest $request): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        $mentor = new Mentor();
        $mentor->fill($request->all());
        $mentor->preco = (int)((float) $request->get('preco') * 100);
        $mentor->user()->associate($user);
        $mentor->save();
        return response()->json($mentor, Response::HTTP_CREATED);
    }

    /**
     * Mostrar Mentor
     *
     * Mostra dados do Mentor especificado
     */
    public function show(Mentor $mentor): \Illuminate\Http\JsonResponse
    {
        return response()->json($mentor, Response::HTTP_OK);
    }

    /**
     * Atualizar Mentor
     *
     * Atualiza dados do Mentor especificado
     */
    public function update(UpdateMentorRequest $request, Mentor $mentor): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        if ($user->id != $mentor->user->id) {
            return response()->json(['message' => 'Somente o próprio usuário pode editar suas informações'], Response::HTTP_FORBIDDEN);
        }
        $mentor->fill($request->all());
        $mentor->user()->associate($user);
        $mentor->save();
        return response()->json($mentor, Response::HTTP_OK);
    }

    /**
     * Apagar Mentor
     *
     * Remove registro do mentor
     */
    public function destroy(Mentor $mentor): \Illuminate\Http\JsonResponse
    {
        $mentor->delete();
        return response()->json(null, Response::HTTP_NO_CONTENT);
    }


    /**
     * Adiciona Habilidade
     *
     * Endpoint para adicionar uma habilidade existente ao mentor logado
     */
    public function addHabilidade(Mentor $mentor, Habilidade $habilidade, Request $request): \Illuminate\Http\JsonResponse
    {
        $valid = $request->validate([
            'certificado' => 'required|file|mimes:pdf|max_file_size:2048'
        ]);
        $user = auth('api')->user();
        if ($user->id != $mentor->user->id) {
            return response()->json(['message' => 'Somente o próprio usuário pode editar suas informações'], Response::HTTP_FORBIDDEN);
        }
        $mentor->habilidades()->attach($habilidade);
        $mentor->save();
        return response()->json($mentor, Response::HTTP_OK);
    }

    /**
     *
     * Configura Cargo
     *
     * Configura qual cargo atualmente ocupado pelo Mentor
     */

    public function setCargo(Mentor $mentor, Cargo $cargo): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        if ($user->id != $mentor->user->id) {
            return response()->json(['message' => 'Somente o próprio usuário pode editar suas informações'], Response::HTTP_FORBIDDEN);
        }
        $mentor->cargo()->associate($cargo);
        $mentor->save();
        return response()->json($mentor, Response::HTTP_OK);
    }

    /**
     *
     * Configura Empresa
     *
     * Configura qual empresa o Mentor está atualmente trabalhando
     */
    public function setEmpresa(Mentor $mentor, Empresa $empresa): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        if ($user->id != $mentor->user->id) {
            return response()->json(['message' => 'Somente o próprio usuário pode editar suas informações'], Response::HTTP_FORBIDDEN);
        }
        $mentor->empresa()->associate($empresa);
        $mentor->save();
        return response()->json($mentor, Response::HTTP_OK);
    }

    /**
     * Minhas Mentorias
     *
     * Lista mentorias do Mentor logado
     */

    public function minhasMentorias(): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        return response()->json(Mentoria::where('mentor_id', $user->id)->get(), Response::HTTP_OK);
    }

    /**
     * Enviar Certificado
     *
     * Endpoint para realizar upload de arquivo pdf contendo certificado para comprovação de habilidade
     */
    public function sendCertificate(Mentor $mentor, Habilidade $habilidade, Request $request): \Illuminate\Http\JsonResponse
    {

        $valid = $request->validate([
            'certificado' => 'required|file|mimes:pdf|max_file_size:2048'
        ]);

        $file = request()->file('certificado');
        if (!$file || !$file->isValid()) {
            return response()->json(['message' => 'Certificado não enviado ou inválido'], Response::HTTP_BAD_REQUEST);
        }

        //Gerar nome do arquivo
        $filename = "mentor_{$mentor->id}_habilidade_{$habilidade->id}_" . str_replace('/', '', $file->getClientOriginalName()) . '.pdf';

        // Salvar arquivo
        try {
            $path = Storage::disk('local')->put(
                'certificados/' . $mentor->id . '/' . $habilidade->id . '/',
                $file,
                ['visibility' => 'private']
            );
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao fazer upload do arquivo'], Response::HTTP_SERVICE_UNAVAILABLE);
        }
        $mentor->habilidades()->attach($habilidade, ['certificado' => $filename]);

        return response()->json([
            'message' => 'Certificado enviado com sucesso',
            'filename' => $filename
        ], Response::HTTP_OK);
    }
}
