@extends('layouts.base')

@section('content')
        <h1>Bienvenue, {{ $first_name }} {{ $last_name }}!</h1>

        <p>Voici vos informations :</p>
    
        <ul>
            <li>ID : {{ $id }}</li>
            <li>Prénom : {{ $first_name }}</li>
            <li>Nom : {{ $last_name }}</li>
            <li>Email : {{ $email }}</li>
            <li>Date de création : {{ $created_at }}</li>
            <li>Dernière mise à jour : {{ $updated_at }}</li>
        </ul>
    
        <form action="/user/logout" method="GET">
            @csrf
            <button type="submit">Se déconnecter</button>
        </form>
@endsection