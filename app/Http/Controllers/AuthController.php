<?php


namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\Role;
use App\Models\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\UserResource;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;

class AuthController extends Controller
{
    /**
     *
     * Registrar Usuário
     *
     * Registra um novo usuário no sistema
     */
    public function register(UserRequest $request): JsonResponse
    {

        $data = $request->all();
        $existing = User::where('email', $data['email'])->get();
        if ($existing->count() > 0) {
            return response()->json(['success' => false, 'message' => 'Usuário já existe'], 400);
        }
        $user = User::create($data);
        $user->save();
        return response()->json(new UserResource($user));
    }

    /**
     *
     * Login
     *
     * Realiza o login de um usuário cadastrado no sistema
     */

    public function login(Request $request): JsonResponse
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);
        $credentials = request(['email', 'password']);
        if (!$token = auth('api')->attempt($credentials)) {
            return response()->json(['message' => 'Credenciais inválidas. Verifique-as'], 401);
        }
        return $this->respondWithToken($token);
    }

    /**
     *
     * Informações do Usuário
     *
     * Traz informações sobre qual o usuário que está logado no sistema
     *
     */

    public function me(): JsonResponse
    {
        $user = auth('api')->user();
        if (!$user) {
            return response()->json(['message' => 'Usuário não identificado. Token de autenticação inexistente ou inválido.'], 401);
        }
        return response()->json(new UserResource(auth('api')->user()));
    }

    /**
     *
     * Logout
     *
     * Realiza o logout do usuário logado
     */

    public function logout(): JsonResponse
    {
        auth('api')->logout();
        return response()->json(['message' => 'Deslogado com sucesso!'], Response::HTTP_OK);
    }

    public function profile(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        $user->update($request->except(['password', 'email']));
        $user->save();
        return response()->json(new UserResource($user),Response::HTTP_OK);
    }

    /**
     *
     * Atualiza Token
     *
     * Faz a atualização do token do usuário, caso ainda não esteja vencido
     */

    public function refresh(): JsonResponse
    {
        return $this->respondWithToken(auth('api')->refresh());
    }

    /**
     * Promove Usuário
     *
     * Endpoint para atribuir perfil de administrador ao usuário informado. Precisa ter perfil de Admin
     */
    public function promote(User $user): JsonResponse
    {
        $roleAdmin = Role::where('name', 'role_admin')->first();
        if ((in_array('role_admin', $user->getRoleNamesAttribute()))) {
            return response()->json(['message' => 'Usuário já é administrador'], Response::HTTP_BAD_REQUEST);
        }
        $user->roles()->attach($roleAdmin->id);
        $user->save();
        $user->refresh();
        return response()->json(new UserResource($user));
    }

    protected function respondWithToken($token): JsonResponse
    {
        return response()->json([
            'token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth('api')->factory()->getTTL() * 60,
        ]);
    }

    /**
     * Enviar Foto de Perfil
     *
     * Endpoint para realizar upload de imagem contendo foto de perfil do usuário
     */
    public function profilePicture(Request $request): JsonResponse
    {
        $user = auth('api')->user();
        $valid = $request->validate([
            'foto_perfil' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ]);

        $file = request()->file('foto_perfil');
        if (!$file || !$file->isValid()) {
            return response()->json(['message' => 'Imagem não enviada ou inválida'], Response::HTTP_BAD_REQUEST);
        }

        // Lê conteúdo do arquivo
        $fileContent = file_get_contents($file->getRealPath());

        $hash = hash('sha256', $fileContent);

        //Gerar nome do arquivo
        $filename = $hash . "." . $file->getClientOriginalExtension();

        // Salvar arquivo
        try {
            $path = $file->storeAs('images/profile', $filename, 'public');
        } catch (\Exception $e) {
            return response()->json(['message' => 'Erro ao fazer upload do arquivo'], Response::HTTP_SERVICE_UNAVAILABLE);
        }
        $user->foto_perfil = $path;
        $user->save();
        return response()->json([
            'success' => true,
            'message' => 'Imagem salva com sucesso!',
            'filename' => $path
        ], Response::HTTP_OK);
    }

}
