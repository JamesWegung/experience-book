<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TelegramLinkController extends Controller
{
    public function generateToken(Request $request): JsonResponse
    {
        $user = $request->user();

        $user->tokens()->where('name', 'telegram-link')->delete();

        $token = $user->createToken(
            'telegram-link',
            ['telegram:link'],
            now()->addMinutes(30)
        );

        return response()->json([
           'token' => $token->plainTextToken,
           'expires_at' => $token->accessToken->expires_at,
           'user' => [
               'id' => $user->id,
               'name' => $user->name,
               'email' => $user->email,
           ]
        ]);
    }

}
