@extends('layouts.base')

@section('content')

<link rel="stylesheet" href="{{ asset('css/app.css') }}">
<form method="POST" action="{{ route('app-register') }}">
    @csrf

    <div>
        <label for="name">Nom de l'application</label>
        <input type="text" name="name" id="name" required>
    </div>

    <div>
        <label for="redirect_uri">URI de redirection</label>
        <input type="text" name="redirect_uri" id="redirect_uri" required>
    </div>

    <button type="submit">Enregistrer</button>
    <a href="/app-login">Se connecter</a>
    <script src="{{ asset('js/app.js') }}"></script>
</form>
@endsection