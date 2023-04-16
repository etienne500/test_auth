@extends('layouts.base')

@section('content')

<!-- Formulaire de connexion -->
<form method="POST" action="{{ route('user-login') }}">

    @if (session('message'))
        <div class="alert alert-{{ session('message')['type'] }}">
            {{ session('message')['text'] }}
        </div>
    @endif

    @csrf
    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <div>
        <h2>Connexion sur Adaa</h2>
        <h3>Appication : {{ $appName }}</h3>
    <div>
        <label for="email">E-mail :</label>
            <input id="email" type="email" name="email" value="{{ old('email') }}" required autofocus>
        </div>
    </div>

    <div>
        <label for="password">Mot de passe :</label>

        <div>
            <input id="password" type="password" name="password" required>
            <input value="{{$access}}" id="access" type="hidden" name="access">
        </div>
    </div>

    <div>
        <div>
            <button type="submit">
                Se connecter
            </button>
            <a href="/register?token={{$token}}">S'inscrire</a>
        </div>
    </div>
    <script src="{{ asset('js/app.js') }}"></script>
</form>

@endsection