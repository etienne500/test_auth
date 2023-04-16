<?php

namespace App\Http\Controllers;

use App\Models\AccessToken;
use App\Models\ClientApp;
use App\Models\User;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Faker\Factory as FakerFactory;
use Illuminate\Support\Facades\Hash;

class AuthenticationController extends Controller
{
    public function authentication(Request $request)
    {
        $access = $request->access;
        $this->logMessage('info', 'Redirected to app-authenticate route', 'access = ' . $access);
        return Redirect::route('app-authenticate', ['access' => $access]);
    }

    public function authentication(Request $request, $access)
    {
        try {
            $decodedAccess = base64_decode($access);
            $this->logMessage('info', 'Access token decoded', 'decoded_access = ' . $decodedAccess);
            $accessParts = explode(':', $decodedAccess);

            if (count($accessParts) !== 2) {
                return Redirect::back()->with('message', [
                    'type' => 'error',
                    'text' => 'Invalid client app.'
                ]);
            }
            list($clientId, $publicKey) = $accessParts;
            $this->logMessage($clientId, Client::class, 'Client ID and public key extracted from access token');

            $clientApp = ClientApp::where('id', $clientId)->firstOrFail();
            $this->logMessage($clientId, Client::class, 'Client app found in the database');

            if ($clientApp->public_key != $publicKey) {
                return Redirect::back()->with('message', [
                    'type' => 'error',
                    'text' => 'Invalid client app.'
                ]);
            }

            $this->logMessage($clientId, Client::class, 'App authenticated');
            return view('user-login', [
                'appName' => $clientApp->name,
                'access' => $decodedAccess,
                'token' => $access
            ]);

        } catch (\Exception $e) {
            $this->logMessage('error', 'Error in authentication method', $e->getMessage());
            return Redirect::back()->with('message', [
                'type' => 'error',
                'text' => 'An error occurred while authenticating.'
            ]);
        }
    }

    public function login(Request $request) {
    try {
        $access = $request->access;
        $decodedAccess = ($access);
    
        list($clientId, $publicKey) = explode(':', $decodedAccess);
    
        $client = ClientApp::where('id', $clientId)
                           ->where('public_key', $publicKey)
                           ->firstOrFail();
        
        if (Auth::attempt(['email' => $request->email, 'password' => $request->password])) {
            $user = Auth::user();
            $faker = FakerFactory::create();
    
            $accessToken = new AccessToken();
            $accessToken->client_id = $client->id;
            $accessToken->id = $faker->uuid();
            $accessToken->user_id = $user->id;
            $accessToken->expires_at = now()->addHours(1); // Expires en 1 heure
            $accessToken->save();
    
            $tokenData = [
                'id' => $accessToken->id,
                'client_id' => $accessToken->client_id,
                'expires_at' => $accessToken->expires_at,
                'user' => $user 
            ];
            $tokenDataJson = json_encode($tokenData);
            $signature = md5($tokenDataJson . $client->secret_key);
            $token = base64_encode("$signature:$tokenDataJson");
    
            $returnUrl = $client->return_url;
            return redirect("$returnUrl?access_token=$token");
        } else {
            return redirect()->back()->with('message', [
                'type' => 'error',
                'text' => 'Invalid email or password'
            ]);
        }
            } catch (Exception $e) {
                // $this->logMessage($e->getMessage(), 'Login Exception');
                return redirect()->back()->with('message', [
                    'type' => 'error',
                    'text' => 'An error occurred during login'
                ]);
            }
        }



        public function authenticate($access)
        {
            try {
                $decoded = base64_decode($access);
                list($client_id, $public_key) = explode(':', $decoded);

                $clientApp = ClientApp::where('id', $client_id)
                    ->where('public_key', $public_key)
                    ->firstOrFail();

                return view('login')->with('clientApp', $clientApp);

            } catch (Exception $e) {
                return redirect()->back()->with('message', [
                    'type' => 'error',
                    'text' => 'Erreur lors de l\'authentification du client'
                ]);
            }
        }

        public function logout(Request $request)
        {
            try {
                $user = $request->user();

                if ($user && $user->currentAccessToken()) {
                    $user->tokens()->delete();
                    $this->logMessage($user->id, User::class, 'Tokens deleted.');
                }

                $this->logMessage($user->id, User::class, 'User logged out.');
                return redirect('/welcome')->with('message', [
                    'type' => 'success',
                    'text' => 'Vous avez été déconnecté avec succès !'
                ]);
                
            } catch (Exception $e) {
                return redirect()->back()->with('message', [
                    'type' => 'error',
                    'text' => 'Erreur lors de la déconnexion'
                ]);
            }
        }

        public function getUser(Request $request)
        {
            try {
                $token = $request->query('token');
                list($signature, $tokenInJson) =  explode(':', base64_decode($token), 2);
                $accessToken = json_decode($tokenInJson);
                
                $client = ClientApp::where('id', '=', $accessToken->client_id)->first();
            
                if (!$client || md5($tokenInJson . $client->secret_key) !== $signature || $accessToken->expires_at < now()) {
                    return response()->json(['message' => 'Token invalide ou expiré.'], 401);
                }
            
                $user = User::where('id', '=', $accessToken->user->id)->first();
            
                $this->logMessage($user->id, User::class, 'Demande d\'informations utilisateur');
                return view('user', [
                                    'id' => $user->id,
                                    'first_name' => $user->first_name,
                                    'last_name' => $user->last_name,
                                    'email' => $user->email,
                                    'created_at' => $user->created_at,
                                    'updated_at' => $user->updated_at
                                ]);
            } catch (Exception $e) {
                return redirect()->back()->with('message', [
                    'type' => 'error',
                    'text' => 'Erreur lors de la récupération des informations utilisateur'
                ]);
            }
        }

        public function welcome(Request $request)
        {
            try {
                $user = $request->user();

                if ($user && $user->currentAccessToken()) {
                    $user->tokens()->delete();
                    $this->logMessage($user->id, User::class, 'Tokens deleted : logout.');
                }
                $this->logMessage(0, 'General', 'Welcome page accessed.');
                return view('welcome');
                
            } catch (Exception $e) {
                return redirect()->back()->with('message', [
                    'type' => 'error',
                    'text' => 'Erreur lors de l\'affichage de la page de bienvenue'
                ]);
            }
        }

        public function userRegister(Request $request)
        {
            try {
                $user = User::create([
                    'first_name' => $request->first_name,
                    'last_name' => $request->last_name,
                    'email' => $request->email,
                    'password' => Hash::make($request->password),
                ]);

                $this->logMessage($user->id, User::class, 'User registered.');
                return redirect('/app-authenticate/' . $request->access);
            } catch (Exception $e) {
                return redirect()->back()->with('message', [
                    'type' => 'error',
                    'text' => 'An error occurred during login'
                ]);
            }
        }

        public function register(Request $request)
        {
            try {
                $token = $request->token;
                $this->logMessage(0, 'General', 'Register page accessed with token: ' . $token);

                return view('user-register', ['access' => $token]);
            } catch (Exception $e) {
                return redirect()->back()->with('message', [
                    'type' => 'error',
                    'text' => 'An error occurred during login'
                ]);
            }
        }

        public function showLogin(Request $request)
        {
            try {
                $token = $request->token;
                $this->logMessage(0, 'General', 'Register page accessed with token: ' . $token);

                return view('welcome');
            } catch (Exception $e) {
                return redirect()->back()->with('message', [
                    'type' => 'error',
                    'text' => 'An error occurred during login'
                ]);
            }
        }
}
