<?php

namespace App\Http\Controllers;

use App\Services\Auth\LoginAttemptLimiterInterface;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;

class AuthController extends Controller
{
    public function login(Request $request, LoginAttemptLimiterInterface $loginThrottle): JsonResponse
    {
        if ($loginThrottle->didReachLimit($request)) {
            return response()->json(['error' => sprintf(
                'Login attempt limit of %d in the last %d seconds has been reached',
                $loginThrottle->getRateLimit(),
                $loginThrottle->getTimeLimit()
            )], Response::HTTP_UNAUTHORIZED);
        }

        $credentials = request(['email', 'password']);

        if (!$token = Auth::attempt($credentials)) {
            $loginThrottle->registerLoginAttempt($request, false);
            return response()->json(['error' => 'Unauthorized'], Response::HTTP_UNAUTHORIZED);
        }

        $loginThrottle->registerLoginAttempt($request, true);
        return $this->respondWithToken($token);
    }

    protected function respondWithToken($token): JsonResponse
    {
        return response()->json([
            'access_token' => $token,
            'token_type' => 'bearer',
            'expires_in' => auth()->factory()->getTTL() * 60
        ]);
    }
}
