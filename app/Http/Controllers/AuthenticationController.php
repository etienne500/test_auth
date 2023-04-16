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

        // On récupère le client
        $client = ClientApp::where('id', $validatedData['client_id'])
            ->where('public_key', $validatedData['public_key'])
            ->firstOrFail();

        // On vérifie les identifiants de l'utilisateur
        if (!Auth::attempt($validatedData)) {
            return response()->json([
                'message' => 'Identifiants invalides'
            ], 401);
        }

        // On crée un token d'accès pour l'utilisateur
        $accessToken = AccessToken::create([
            'client_id' => $client->id,
            'user_id' => Auth::id(),
            'expires_at' => now()->addHours(1), // Expire dans 1 heure
        ]);

        // On crée la signature pour le token
        $tokenData = [
            'id' => $accessToken->id,
            'client_id' => $client->id,
            'expires_at' => $accessToken->expires_at->timestamp,
            'user' => Auth::user(), // en assument que les relations ont été définies
        ];
        $tokenJson = json_encode($tokenData);
        $signature = md5($tokenJson . $client->secret_key);
        $token = base64_encode("$signature:$tokenJson");

        return redirect('/home');

        // return response()->json([
        //     'access_token' => $token
        // ]);
    }

    public function getUser(Request $request)
    {
        $token = $request->query('token');
        
        // Vérifier que le token est bien présent dans la requête
        if (!$token) {
            return response()->json(['error' => 'Token manquant'], 401);
        }
        
        // Décoder le token à partir de sa base64
        $decodedToken = base64_decode($token);
        
        // Séparer la signature et les données du token
        $tokenParts = explode(':', $decodedToken);
        
        // Vérifier qu'il y a bien deux parties dans le token
        if (count($tokenParts) !== 2) {
            return response()->json(['error' => 'Token invalide'], 401);
        }
        
        $clientSignature = $tokenParts[0];
        $tokenData = $tokenParts[1];
        
        // Vérifier que la signature du token correspond à celle attendue
        $client = ClientApp::where('public_key', $request->query('public_key'))->first();
        if (!$client) {
            return response()->json(['error' => 'Client inconnu'], 401);
        }
        
        $expectedSignature = md5($tokenData . $client->secret_key);
        if ($clientSignature !== $expectedSignature) {
            return response()->json(['error' => 'Signature invalide'], 401);
        }
        
        // Décoder les données du token au format JSON
        $tokenData = json_decode($tokenData, true);
        
        // Vérifier que le token est encore valide
        if (time() > strtotime($tokenData['expires_at'])) {
            return response()->json(['error' => 'Token expiré'], 401);
        }
        
        // Récupérer l'utilisateur associé au token
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

    ///app

    public function APPauthenticate($access)
    {
        // On décode le paramètre $access qui est en base64
        $decodedAccess = base64_decode($access);
        
        // On extrait le client_id et public_key de $decodedAccess
        list($clientId, $publicKey) = explode(':', $decodedAccess);
        
        // On récupère l'application cliente correspondante
        $clientApp = ClientApp::where('id', $clientId)
                            ->where('public_key', $publicKey)
                            ->firstOrFail();

        // On affiche la page de connexion en passant en paramètre le nom de l'application cliente
        return view('login', ['appName' => $clientApp->name]);
    }

    public function APPlogin(Request $request)
    {
        // Récupération des données envoyées depuis le formulaire de login de l'application cliente
        $clientAccessData = $request->input('client_access_data'); // Données d'accès de l'application cliente encodées en base64
        $email = $request->input('email');
        $password = $request->input('password');

        // Décodage des données d'accès de l'application cliente
        $decodedClientAccessData = base64_decode($clientAccessData);
        [$clientId, $publicKey] = explode(':', $decodedClientAccessData);

        // Récupération de l'application cliente correspondante
        $clientApp = ClientApp::where('id', $clientId)->where('public_key', $publicKey)->first();

        // Vérification de l'existence de l'application cliente
        if (!$clientApp) {
            return response()->json(['error' => 'Invalid client access data'], 400);
        }

        // Vérification de l'existence de l'utilisateur correspondant aux informations de login fournies
        $user = User::where('email', $email)->first();
        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 400);
        }

        // Création du token d'accès
        $accessToken = new AccessToken([
            'client_id' => $clientApp->id,
            'user_id' => $user->id,
            'expires_at' => now()->addDay(),
        ]);
        $accessToken->save();

        // Génération du token à retourner
        $tokenData = [
            'id' => $accessToken->id,
            'client_id' => $clientApp->id,
            'expires_at' => $accessToken->expires_at,
            'user' => $user,
        ];
        $tokenDataJson = json_encode($tokenData);
        $signature = md5($tokenDataJson . $clientApp->secret_key);
        $token = base64_encode("$signature:$tokenDataJson");

        // Retour de la réponse contenant le token
        return response()->json(['access_token' => $token]);
    }

    public function getApp(Request $request)
    {
        $token = $request->query('token');
        
        // Vérifier si le token est valide
        $accessToken = AccessToken::where('token', $token)->firstOrFail();
        
        // Obtenir les informations de l'application cliente associée au token
        $clientApp = $accessToken->clientApp;
        
        // Retourner les informations de l'application
        return response()->json([
            'id' => $clientApp->id,
            'name' => $clientApp->name,
            'return_url' => $clientApp->return_url,
        ]);
    }


    public function registerApp(Request $request)
    {
        // Validation des données reçues depuis le formulaire
        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'return_url' => 'required|url|max:255',
            'public_key' => 'required|string|max:255',
            'secret_key' => 'required|string|max:255',
        ]);

        // Création d'une nouvelle instance de ClientApp
        $clientApp = new ClientApp();

        // Assignation des valeurs depuis les données validées
        $clientApp->name = $validatedData['name'];
        $clientApp->return_url = $validatedData['return_url'];
        $clientApp->public_key = $validatedData['public_key'];
        $clientApp->secret_key = $validatedData['secret_key'];

        // Enregistrement du nouveau client d'application
        $clientApp->save();

        // Retour de la réponse JSON avec les détails du nouveau client d'application
        return response()->json([
            'message' => 'Client application created successfully',
            'client_app' => $clientApp
        ], 201);
    }

    public function authenticateApp($access)
    {
        // Décodage de l'access
        $accessDecoded = base64_decode($access);
        // Extraction de client_id et public_key
        list($clientId, $publicKey) = explode(':', $accessDecoded);

        // Récupération de l'application cliente à partir de client_id et public_key
        $client = ClientApp::where('id', $clientId)
                        ->where('public_key', $publicKey)
                        ->firstOrFail();

        // Récupération du nom de l'application
        $appName = $client->name;

        // Affichage de la page de connexion pour l'utilisateur
        return view('login', compact('appName'));
    }

    public function loginApp(Request $request)
    {
        // récupérer les identifiants du client depuis le header Authorization
        $authHeader = $request->header('Authorization');
        if (!$authHeader) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // extraire les identifiants depuis l'en-tête Authorization
        $credentials = explode(':', base64_decode(str_replace('Basic ', '', $authHeader)));
        $clientId = $credentials[0];
        $clientSecret = $credentials[1];

        // récupérer le client correspondant aux identifiants fournis
        $client = ClientApp::where('id', $clientId)->where('secret_key', $clientSecret)->first();
        if (!$client) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // récupérer les données de connexion envoyées dans la requête
        $email = $request->input('email');
        $password = $request->input('password');

        // trouver l'utilisateur correspondant à ces données de connexion
        $user = User::where('email', $email)->first();
        if (!$user || !Hash::check($password, $user->password)) {
            return response()->json(['error' => 'Unauthorized'], 401);
        }

        // générer un jeton d'accès pour l'utilisateur et le client correspondant
        $accessToken = new AccessToken();
        $accessToken->client_id = $client->id;
        $accessToken->user_id = $user->id;
        $accessToken->expires_at = Carbon::now()->addHours(8);
        $accessToken->save();

        // construire la réponse avec le jeton d'accès généré
        $tokenInJson = json_encode([
            'id' => $accessToken->id,
            'client_id' => $accessToken->client_id,
            'expires_at' => $accessToken->expires_at,
            'user' => $accessToken->user
        ]);
        $signature = md5($tokenInJson . $client->secret_key);
        $token = base64_encode("$signature:$tokenInJson");

        // // Redirect to the return URL
        // return redirect(session('return_url'));

        // retourner la réponse avec le jeton d'accès
        // return response()->json(['access_token' => $token]);
        
        return redirect('/home');
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
