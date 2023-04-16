<?php

namespace App\Http\Controllers;

use App\Models\AccessToken;
use App\Models\ClientApp;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    public function authenticate($access)
    {
        $decoded = base64_decode($access);
        list($client_id, $public_key) = explode(':', $decoded);

        $clientApp = ClientApp::where('id', $client_id)
            ->where('public_key', $public_key)
            ->firstOrFail();

        return view('login')->with('clientApp', $clientApp);
    }

    public function login(Request $request)
    {
        $validatedData = $request->validate([
            'email' => 'required|email',
            'password' => 'required',
            'client_id' => 'required',
            'public_key' => 'required',
        ]);

        $client = ClientApp::where('id', $validatedData['client_id'])
            ->where('public_key', $validatedData['public_key'])
            ->firstOrFail();

        if (!Auth::attempt($validatedData)) {
            return response()->json([
                'message' => 'Identifiants invalides'
            ], 401);
        }

        $accessToken = AccessToken::create([
            'client_id' => $client->id,
            'user_id' => Auth::id(),
            'expires_at' => now()->addHours(1), // Expire dans 1 heure
        ]);

        $tokenData = [
            'id' => $accessToken->id,
            'client_id' => $client->id,
            'expires_at' => $accessToken->expires_at->timestamp,
            'user' => Auth::user(), 
        ];
        $tokenJson = json_encode($tokenData);
        $signature = md5($tokenJson . $client->secret_key);
        $token = base64_encode("$signature:$tokenJson");

        return redirect('/home');

    }

    public function getUser(Request $request)
    {
        $token = $request->query('token');
        
        if (!$token) {
            return response()->json(['error' => 'Token manquant'], 401);
        }
        
        $decodedToken = base64_decode($token);
        
        $tokenParts = explode(':', $decodedToken);
        
        if (count($tokenParts) !== 2) {
            return response()->json(['error' => 'Token invalide'], 401);
        }
        
        $clientSignature = $tokenParts[0];
        $tokenData = $tokenParts[1];
        
        $client = ClientApp::where('public_key', $request->query('public_key'))->first();
        if (!$client) {
            return response()->json(['error' => 'Client inconnu'], 401);
        }
        
        $expectedSignature = md5($tokenData . $client->secret_key);
        if ($clientSignature !== $expectedSignature) {
            return response()->json(['error' => 'Signature invalide'], 401);
        }
        
        $tokenData = json_decode($tokenData, true);
        
        if (time() > strtotime($tokenData['expires_at'])) {
            return response()->json(['error' => 'Token expiré'], 401);
        }
        
        $user = User::where('id', $tokenData['user']['id'])->first();
        if (!$user) {
            return response()->json(['error' => 'Utilisateur inconnu'], 401);
        }
        
        return response()->json([
            'user' => [
                'id' => $user->id,
                'first_name' => $user->first_name,
                'last_name' => $user->last_name,
                'email' => $user->email,
            ]
        ]);
    }

    public function registerUser(Request $request)
    {
        $this->validate($request, [
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:6|confirmed',
        ]);

        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        return redirect('/login');
    }


    public function showRegisterUser(Request $request)
    {
        return view('user-register');
    }


    public function showLoginUser(Request $request)
    {
        return view('user-login');
    }


    public function showRegisterApp(Request $request)
    {
        return view('app-register');
    }


    public function showLoginApp(Request $request)
    {
        return view('app-login');
    }

}
