<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthenticationController;
use App\Http\Controllers\ApplicationController;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\ClientApp;
use App\Models\User;
use App\Models\AccessToken;
use App\Models\Log;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Hash;
use Faker\Factory as FakerFactory;
use GuzzleHttp\Client;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/


    function logMessage($loggableId, $loggableType, $message) {
        try {
            Log::create([
                'loggable_id' => $loggableId,
                'loggable_type' => $loggableType,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('message', [
                'type' => 'error',
                'text' => $e->getMessage()
            ]);
        }
    }
    
    
    Route::get('/app-authentication', function(Request $request)
    {      
        $access = $request->access;
        logMessage('info', 'Redirected to app-authenticate route', 'access = '. $access);
        return redirect()->route('app-authenticate', ['access' => $access]);

    })->name('app-authentication');


    Route::get('/app-authenticate/{access}', function(Request $request, $access)
    {        
        $decodedAccess = base64_decode($access);
        logMessage('info', 'Access token decoded', 'decoded_access = ' . $decodedAccess);
        $accessParts = explode(':', $decodedAccess);

        
        if (count($accessParts) !== 2) {
            return redirect()->back()->with('message', [
                'type' => 'error',
                'text' => 'Invalid client app.'
            ]);
        }
        list($clientId, $publicKey) = $accessParts;
        logMessage($clientId, Client::class, 'Client ID and public key extracted from access token');


        logMessage($clientId, Client::class, 'Client app found in the database');
        $clientApp = ClientApp::where('id', $clientId)->first();
    
        if (!$clientApp || $clientApp->public_key != $publicKey) {
            logMessage($clientId, Client::class, 'App fail authenticated');
            return redirect()->back()->with('message', [
                'type' => 'error',
                'text' => 'Invalid client app.'
            ]);
        }
    
        logMessage($clientId, Client::class, 'App authenticated');
        return view('user-login', ['appName' => $clientApp->name,'access' => $decodedAccess, 'token' => $access]);
    })->name('app-authenticate');

    Route::post('/user/login', function(Request $request) {
        $access = $request->access;
        $decodedAccess = ($access);

        list($clientId, $publicKey) = explode(':', $decodedAccess);
    
        $client = ClientApp::where('id', $clientId)
                           ->where('public_key', $publicKey)
                           ->firstOrFail();
        logMessage($clientId, Client::class, 'Client app found in the database');
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
            logMessage($user->id, User::class, 'signature of user_id = '. $user->id);


            $returnUrl = $client->return_url;
            return redirect("$returnUrl?access_token=$token");
    
        } else {
        
        logMessage($request->email, User::class, 'Invalid email or password');
            return redirect()->back()->with('message', [
                'type' => 'error',
                'text' => 'Invalid email or password'
            ]);
        }
        
    })->name("user-login");

    Route::get('/user', function(Request $request)
    {
        $token = $request->query('token');
        list($signature, $tokenInJson) =  explode(':', base64_decode($token), 2);
        $accessToken = json_decode($tokenInJson);
        
        $client = ClientApp::where('id', '=', $accessToken->client_id)->first();
    
        if (!$client || md5($tokenInJson . $client->secret_key) !== $signature || $accessToken->expires_at < now()) {
            return response()->json(['message' => 'Token invalide ou expiré.'], 401);
        }
    
        $user = User::where('id', '=', $accessToken->user->id)->first();
    
        logMessage($user->id, User::class, 'Demande d\'informations utilisateur');
        return view('user', [
                            'id' => $user->id,
                            'first_name' => $user->first_name,
                            'last_name' => $user->last_name,
                            'email' => $user->email,
                            'created_at' => $user->created_at,
                            'updated_at' => $user->updated_at
                        ]);
    });

    
    Route::get('/user/logout', function(Request $request)
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->tokens()->delete();
            logMessage($user->id, User::class, 'Tokens deleted.');
        }

        logMessage($user->id, User::class, 'User logged out.');
        return redirect('/welcome')->with('message', [
            'type' => 'success',
            'text' => 'Vous avez été déconnecté avec succès !'
        ]);
        
    });    
    
    Route::get('/welcome', function(Request $request)
    {
        $user = $request->user();

        if ($user && $user->currentAccessToken()) {
            $user->tokens()->delete();
            logMessage($user->id, User::class, 'Tokens deleted : logout.');
        }
        logMessage(0, 'General', 'Welcome page accessed.');
        return view('welcome');
        
    });

    Route::post('/user-register', function(Request $request)
    {
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        logMessage($user->id, User::class, 'User registered.');
        return redirect('/app-authenticate/' . $request->access);
    })->name("user-register");
    
    Route::get('/register', function(Request $request)
    {
        $token = $request->token;
        logMessage(0, 'General', 'Register page accessed with token: ' . $token);

        return view('user-register', ['access' => $token]);
    })->name("register");

    Route::get('/', function(Request $request)
    {
        $token = $request->token;
        logMessage(0, 'General', 'Register page accessed with token: ' . $token);

        return view('welcome');
    })->name("login");

    Route::get('/applications', 
    function(Request $request){
        $ClientApps = ClientApp::all();
        return view('clientappsIndex', ['applications' => $ClientApps]);
    }
    )->name('applications.index');
    
    Route::get('/applications/create', function(Request $request) {
        return view('showCreateApp');
    })->name('applications.create');
    
    Route::get('/applications/{id}', function(Request $request, $id) {
        $client = ClientApp::find($id);
        return view('showApp', ["application" => $client]);
    })->name("applications.show");
    
    Route::post('/applications', function(Request $request)
    {        
        $faker = FakerFactory::create();

        $ClientApp = new ClientApp();
        $ClientApp->name = $request->input('name');
        $ClientApp->return_url = $request->input('url');
        $ClientApp->id = $faker->uuid();
        $ClientApp->public_key = md5(uniqid('', true));
        $ClientApp->secret_key = md5(uniqid('', true));
        $ClientApp->save();

        return redirect("/applications");
    })->name('applications.store');
    
    Route::get('/applications/{id}/edit', function(Request $request, $id) {
        $client = ClientApp::find($id);
        return view('showUpdateApp', ["application" => $client]);
    })->name("applications.edit");
    
    Route::put('/applications/{id}', function(Request $request, $id)
    {
        
        $user = Auth::user();
        $faker = FakerFactory::create();

        $ClientApp = ClientApp::findOrFail($id);
        $ClientApp->name = $request->input('name');
        $ClientApp->save();

        return redirect('/applications');
    })->name('applications.update');
    
    Route::delete('/applications/{id}', function(Request $request, $id) {
        ClientApp::destroy($id);
        return redirect()->route('applications.index');
    })->name('applications.destroy');

