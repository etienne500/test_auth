@extends('layouts.base')

@section('content')

<form method="POST" action="{{ route('app-login') }}">
    @csrf

    <div>
        <label for="public_key">Clé publique</label>
        <input type="text" name="public_key" id="public_key" required>
    </div>

    <div>
        <label for="secret_key">Clé secrète</label>
        <input type="password" name="secret_key" id="secret_key" required>
    </div>

    <button type="submit">Se connecter</button>
    <a href="/app-register">S'inscrire</a>
    
</form>@endsection