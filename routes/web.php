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

    
    Route::group([], function () {

        Route::get('/app-authentication', "App\Http\Controllers\AuthenticationController@authentication")->name('app-authentication');
    
        Route::get('/app-authenticate/{access}', "App\Http\Controllers\AuthenticationController@authenticafition")->name('app-authenticate');
    
        Route::post('/user/login',"App\Http\Controllers\AuthenticationController@login")->name("user-login");
    
        Route::get('/user', "App\Http\Controllers\AuthenticationController@getUser");
    
        Route::get('/user/logout',"App\Http\Controllers\AuthenticationController@logout");
    
        Route::get('/welcome', "App\Http\Controllers\AuthenticationController@welcome");
    
        Route::post('/user-register', "App\Http\Controllers\AuthenticationController@userRgister")->name("user-register");
    
        Route::get('/register',"App\Http\Controllers\AuthenticationController@register")->name("register");
    
        Route::get('/', "App\Http\Controllers\AuthenticationController@showLogin")->name("login");
    
    });
    
    Route::group([], function () {
        Route::get('/applications', "App\Http\Controllers\ApplicationController@list")->name('applications.index');
        
        Route::get('/applications/create', "App\Http\Controllers\ApplicationController@showCreate")->name('applications.create');
        
        Route::get('/applications/{id}', "App\Http\Controllers\ApplicationController@showApp")->name("applications.show");
        
        Route::post('/applications', "App\Http\Controllers\ApplicationController@create")->name('applications.store');
        
        Route::get('/applications/{id}/edit', "App\Http\Controllers\ApplicationController@showUpdate")->name("applications.edit");
        
        Route::put('/applications/{id}', "App\Http\Controllers\ApplicationController@update")->name('applications.update');
        
        Route::delete('/applications/{id}', "App\Http\Controllers\ApplicationController@delete")->name('applications.destroy');
    });