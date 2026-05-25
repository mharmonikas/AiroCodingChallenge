<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use PHPOpenSourceSaver\JWTAuth\JWTGuard;

class AuthController extends Controller
{
    public function login(Request $request): JsonResponse
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            /** @var JWTGuard $guard */
            $guard = auth('api');

            $token = $guard->attempt($credentials) ?: null;

            if ($token === null) {
                return response()->json(['message' => 'Invalid credentials.'], JsonResponse::HTTP_UNAUTHORIZED);
            }

            return response()->json(['token' => $token]);
        } catch (\Throwable) {
            return response()->json(['message' => 'Invalid credentials.'], JsonResponse::HTTP_UNAUTHORIZED);
        }
    }

    public function logout(): JsonResponse
    {
        /** @var JWTGuard $guard */
        $guard = auth('api');
        $guard->logout();

        return response()->json(['message' => 'Logged out.']);
    }
}
