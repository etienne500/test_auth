<?php

namespace App\Http\Middleware;

use App\Models\ClientApp;
use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckAccessMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
                // Récupère le token d'accès dans l'URL
                $access = $request->route('access');
        
                // Décodage du token d'accès pour récupérer le client_id et le public_key
                $decodedAccess = base64_decode($access);
                list($clientId, $publicKey) = explode(':', $decodedAccess);
                
                // Récupère l'application cliente à partir du client_id
                $clientApp = ClientApp::find($clientId);
                if (!$clientApp) {
                    return response()->json(['error' => 'Client not found'], 401);
                }
                
                // Vérifie que la public_key de l'application cliente correspond à celle envoyée dans le token d'accès
                if ($clientApp->public_key != $publicKey) {
                    return response()->json(['error' => 'Invalid public key'], 401);
                }
                
                // Ajoute les informations du clientApp et du token d'accès à la requête pour une utilisation ultérieure
                $request->attributes->add(['clientApp' => $clientApp, 'access' => $access]);
                
                return $next($request);
    }
}
