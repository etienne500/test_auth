@extends('layouts.base')

@section('content')

<!-- Formulaire d'enregistrement -->


<form method="GET" action="{{ route('app-authentication') }}">
    @csrf

    <link rel="stylesheet" href="{{ asset('css/app.css') }}">
    <div>
        <label for="name">Veuillez entrer les données d'une application :</label>

        <div>
            <input id="name" placeholder="base64('client_id:public_key')" type="text" name="access" value="{{ old('access') }}" required autofocus>
        </div>
    </div>

    </div>

    <div>
        <div>
            <button type="submit">
                Se connecter
            </button>
        </div>
    </div>
    
    <script src="{{ asset('js/app.js') }}"></script>
</form>
@endsection