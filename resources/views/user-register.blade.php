@extends('layouts.base')

@section('content')

<!-- Formulaire d'enregistrement -->
<form method="POST" action="{{ route('user-register') }}">
    @csrf

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <div>
        <label for="name">Nom :</label>

        <div>
            <input id="name" type="text" name="first_name" value="{{ old('first_name') }}" required autofocus>
        </div>
    </div
    >
    <div>
        <label for="name">Nom :</label>

        <div>
            <input id="name" type="text" name="last_name" value="{{ old('last_name') }}" required autofocus>
        </div>
    </div>

    <div>
        <label for="email">E-mail :</label>

        <div>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required>
        </div>
    </div>

    <div>
        <label for="password">Mot de passe :</label>

        <div>
            <input id="password" type="password" name="password" required>
        </div>
    </div>

    <div>
        <label for="password_confirmation">Confirmation du mot de passe :</label>

        <div>
            <input id="password_confirmation" type="password" name="password_confirmation" required>
        </div>
        
        <input value="{{$access}}" id="access" type="hidden" name="access">

    </div>

    <div>
        <div>
            <button type="submit">
                S'inscrire
            </button>
            <a href="/app-authenticate/{{$access}}">Se connecter</a>
        </div>
    </div>
    
    <script src="{{ asset('js/app.js') }}"></script>
</form>
@endsection