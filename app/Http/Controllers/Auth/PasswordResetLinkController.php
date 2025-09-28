<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Jobs\SendEmail;
use App\Mail\PasswordResetMail;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class PasswordResetLinkController extends Controller
{
    /**
     * Handle an incoming password reset link request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        $status = Password::sendResetLink(
            $request->only('email')
        );

        if ($status == Password::RESET_LINK_SENT){
            return response()->json(['success' => true, 'message' => 'Link de redefinição de senha enviado']);
        }

        throw ValidationException::withMessages([
            'email' => [trans($status)]
        ]);

        // $user = Password::getUser($request->only('email'));

        // if (!$user) {
        //     throw ValidationException::withMessages([
        //         'email' => [__('passwords.user')],
        //     ]);
        // }

        // // Generate token and store it
        // $token = Password::createToken($user);

        // // Dispatch job to send the email
        // SendEmail::dispatch(
        //     $request->email,
        //     new PasswordResetMail($token)
        // );

        return response()->json(['status' => __('passwords.sent')]);
    }

    /**
     * Test email sending functionality.
     */
    public function testEmail(Request $request): JsonResponse
    {
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Generate a test token
        $token = Str::random(60);

        // Dispatch the job to send the test email
        SendEmail::dispatch(
            $request->email,
            new PasswordResetMail($token)
        );

        return response()->json([
            'message' => 'Email de teste enviado com sucesso para ' . $request->email,
            'token' => $token // Apenas para fins de teste
        ]);
    }
}
