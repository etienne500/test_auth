<?php

namespace App\Http\Middleware;

use App\Models\AccessToken;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckTokenMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $token = $request->session()->get('access_token');
        if (!$token) {
            return redirect('/authenticate');
        }

        $accessToken = AccessToken::where('token', $token)->first();

        if (!$accessToken || $accessToken->isExpired()) {
            return redirect('/authenticate');
        }

        return $next($request);
    }
}
