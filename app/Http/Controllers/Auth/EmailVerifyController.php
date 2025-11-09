<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Verified;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmailVerifyController extends Controller
{
    public function __invoke(Request $request): JsonResponse
    {
        if (!$request->hasValidSignature()) {
            return response()->json(['message' => 'URL inválida o expirada'], 403);
        }

        $user = User::find($request->route('id'));

        if (!$user) {
            return response()->json(['message' => 'Usuario no encontrado'], 404);
        }

        if (!hash_equals((string) $request->route('hash'), sha1($user->getEmailForVerification()))) {
            return response()->json(['message' => 'URL de verificación inválida'], 403);
        }

        if ($user->hasVerifiedEmail()) {
            return response()->json(['message' => 'Email ya verificado'], 200);
        }

        if ($user->markEmailAsVerified()) {
            event(new Verified($user));
        }

        $user->tokens()->delete();
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Correo verificado correctamente',
            'token' => $token,
            'user' => $user
        ], 200);
    }
}
