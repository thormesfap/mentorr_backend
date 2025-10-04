<?php

namespace App\Http\Controllers;

use Symfony\Component\HttpFoundation\Response;
use Illuminate\Http\Request;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Log;
use App\Models\Mentoria;
use App\Models\Mentor;
use App\Models\Habilidade;
use App\Models\Empresa;
use App\Models\Cargo;
use App\Http\Requests\UpdateMentorRequest;
use App\Http\Requests\StoreMentorRequest;
use App\Events\Teste;

class MentorController extends Controller
{
    /**
     * Listar Mentores
     *
     * Lista todos os mentores cadastrados
     */
    public function index(Request $request): \Illuminate\Http\JsonResponse
    {
        $perPage = $request->get('per_page', Mentor::PER_PAGE);
        if (!$request->get('area') && !$request->get('empresa') && !$request->get('cargo')) {
            return response()->json(Mentor::paginate($perPage), Response::HTTP_OK);
        }
        $query = Mentor::query();
        [$cargo, $empresa, $area] = [$request->get('cargo'), $request->get('empresa'), $request->get('area')];
        if ($cargo) {
            $query->whereHas(
                'cargo',
                function ($query) use ($cargo) {
                    $query->where('nome', 'LIKE', "%{$cargo}%");
                }
            );
        }
        if ($empresa) {
            $query->whereHas('empresa', function ($query) use ($empresa) {
                $query->where('nome', 'LIKE', "%{$empresa}%");
            });
        }
        if ($area) {
            $query->whereHas('habilidades', function ($query) use ($area) {
                $query->whereHas('area', function ($subQuery) use ($area) {
                    $subQuery->where('nome', 'LIKE', "%{$area}%");
                });
            });
        }
        return response()->json($query->paginate($perPage), Response::HTTP_OK);
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
        $mentor->preco = (int)((float)$request->get('preco') * 100);
        $mentor->user()->associate($user);
        $mentor->save();
        return response()->json($mentor, Response::HTTP_CREATED);
    }

    /**
     * Mostrar Mentor
     *
     * Mostra dados do Mentor especificado
     */
    public function show(int $id): \Illuminate\Http\JsonResponse
    {
        $mentor = Mentor::with('mentorias')->find($id);
        if (!$mentor) {
            return response()->json(['message' => 'Mentor não encontrado'], Response::HTTP_NOT_FOUND);
        }
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
    public function addHabilidade(Habilidade $habilidade, Request $request): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        $mentor = Mentor::where('user_id', $user->id)->get()->first();
        if (!$mentor) {
            return response()->json(['success' => false, 'message' => 'Usuário não é mentor'], Response::HTTP_FORBIDDEN);
        }
        $mentor->habilidades()->attach($habilidade);
        $mentor->save();
        $mentor->refresh();
        return response()->json($mentor, Response::HTTP_OK);
    }


    /**
     * Seta habilidades
     *
     * Seta uma lista de habilidades para o mentor logado, apagando as existentes
     */

    public function setHabilidades(Request $request, Mentor $mentor): \Illuminate\Http\JsonResponse
    {
        $valid = $request->validate([
            'habilidades' => 'required|array',
        ]);
        $habilidades = $valid['habilidades'];
        $existentes = Habilidade::whereIn('id', $habilidades)->get();
        if (count($existentes) != count($habilidades)) {
            return response()->json(['success' => false, 'message' => 'Foi informado id de habilidade inexistente'],  Response::HTTP_BAD_REQUEST);
        }
        $mentor->habilidades()->sync($valid['habilidades']);
        $mentor->save();
        $mentor->refresh();
        return response()->json($mentor, Response::HTTP_OK);
    }

    /**
     *
     * Configura Cargo
     *
     * Configura qual cargo atualmente ocupado pelo Mentor
     */

    public function setCargo(Cargo $cargo): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        $mentor = Mentor::where('user_id', $user->id)->get()->first();
        if (!$mentor) {
            return response()->json(['sucess' => false, 'message' => 'Usuário não é mentor'], Response::HTTP_FORBIDDEN);
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
    public function setEmpresa(Empresa $empresa): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        $mentor = Mentor::where('user_id', $user->id)->get()->first();
        if (!$mentor) {
            return response()->json(['sucess' => false, 'message' => 'Usuário não é mentor'], Response::HTTP_FORBIDDEN);
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
    public function sendCertificate(Habilidade $habilidade, Request $request): \Illuminate\Http\JsonResponse
    {
        $user = auth('api')->user();
        $mentor = Mentor::where('user_id', $user->id)->get()->first();
        if (!$mentor) {
            return response()->json(['sucess' => false, 'message' => 'Usuário não é mentor'], Response::HTTP_FORBIDDEN);
        }
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
