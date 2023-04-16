@extends('layouts.base')

@section('content')
    <div class="container">
        <h1>Liste des applications</h1>

        <table class="table">
            <thead>
                <tr>
                    <th>Nom</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($applications as $application)
                    <tr>
                        <td>{{ $application->name }}</td>
                        <td>
                            <a href="{{ route('applications.show', ['id' => $application->id]) }}" class="btn btn-primary">Voir</a>
                            <a href="{{ route('applications.edit', ['id' => $application->id]) }}" class="btn btn-warning">Modifier</a>
                            <form action="{{ route('applications.destroy', ['id' => $application->id]) }}" method="POST" style="display:inline;">
                                {{ csrf_field() }}
                                {{ method_field('DELETE') }}
                                <button type="submit" class="btn btn-danger">Supprimer</button>
                            </form>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
        <a href="{{ route('applications.create') }}" class="btn btn-success">Ajouter une application</a>
    </div>
@endsection
