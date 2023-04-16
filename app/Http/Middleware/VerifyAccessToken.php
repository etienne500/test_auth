<?php

namespace App\Http\Middleware;

use App\Models\AccessToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class VerifyAccessToken
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->input('token');

        if (!$token) {
            return response()->json(['error' => 'Token is missing.'], 400);
        }

        $accessToken = AccessToken::where('id', '=', $token)
            ->where('expires_at', '>', now())
            ->first();

        if (!$accessToken) {
            return response()->json(['error' => 'Invalid or expired token.'], 401);
        }

        // Add the user to the request so it can be accessed by the controller
        $request->merge(['user' => $accessToken->user]);

        return $next($request);
    }
}
