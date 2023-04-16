<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class Authenticate
{
    public function handle(Request $request, Closure $next, ...$guards)
    {
        if (empty($guards)) {
            $guards = [null];
        }

        foreach ($guards as $guard) {
            if (Auth::guard($guard)->check()) {
                return $next($request);
            }
        }

        // Vérification de la connexion d'une application cliente
        $token = $request->bearerToken();
        if ($token && $client = \App\Models\ClientApp::where('access_token', $token)->first()) {
            return $next($request);
        }

        return redirect()->route('login');
    }
}
