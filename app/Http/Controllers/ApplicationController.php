<?php

namespace App\Http\Controllers;

use App\Models\ClientApp;
use Illuminate\Http\Request;

class ApplicationController extends Controller
{
    // Affiche la liste de toutes les ClientApps
    public function index()
    {
        $ClientApps = ClientApp::all();
        return view('applications.index', ['applications' => $ClientApps]);
    }

    // Affiche une ClientApp spécifique
    public function show($id)
    {
        $ClientApp = ClientApp::findOrFail($id);
        return view('clientappsIndex', ['applications' => $ClientApp]);
    }

    // Enregistre une nouvelle ClientApp
    public function store(Request $request)
    {
        $ClientApp = new ClientApp();
        $ClientApp->name = $request->input('name');
        $ClientApp->description = $request->input('description');
        $ClientApp->save();

        return redirect("applications.index");
        // return redirect('/ClientApps');
    }

    // Met à jour une ClientApp existante
    public function update(Request $request, $id)
    {
        $ClientApp = ClientApp::findOrFail($id);
        $ClientApp->name = $request->input('name');
        $ClientApp->description = $request->input('description');
        $ClientApp->save();

        return redirect('/ClientApps');
    }

    // Supprime une ClientApp existante
    public function destroy($id)
    {
        $ClientApp = ClientApp::findOrFail($id);
        $ClientApp->delete();

        return redirect('/ClientApps');
    }
}
