@extends('layouts.base')

@section('content')
    <div class="container">
        <h1>Ajouter une application</h1>

        <form action="{{ route('applications.store') }}" method="POST">
            {{ csrf_field() }}

            <div class="form-group">
                <label for="name">Nom :</label>
                <input type="text" name="name" id="name" class="form-control" required>
            </div>

            <div class="form-group">
                <label for="name">Return url :</label>
                <input type="text" name="url" id="name" class="form-control" required>
            </div>

            <button type="submit" class="btn btn-success">Enregistrer</button>
        </form>
    </div>
@endsection
