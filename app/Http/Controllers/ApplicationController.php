<?php

namespace App\Http\Controllers;

use App\Models\ClientApp;
use Faker\Factory as FakerFactory;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class ApplicationController extends Controller
{
    public function list(Request $request)
    {
        try {
            $ClientApps = ClientApp::all();
            return view('clientappsIndex', ['applications' => $ClientApps]);
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while fetching the list of applications.');
        }
    }

    public function showCreate(Request $request)
    {
        try {
            return view('showCreateApp');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while trying to create a new application.');
        }
    }

    public function showApp(Request $request, $id)
    {
        try {
            $client = ClientApp::find($id);
            return view('showApp', ["application" => $client]);
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while trying to display the application details.');
        }
    }

    public function create(Request $request)
    {
        try {
            $faker = FakerFactory::create();

            $ClientApp = new ClientApp();
            $ClientApp->name = $request->input('name');
            $ClientApp->return_url = $request->input('url');
            $ClientApp->id = $faker->uuid();
            $ClientApp->public_key = md5(uniqid('', true));
            $ClientApp->secret_key = md5(uniqid('', true));
            $ClientApp->save();

            $this->logMessage($ClientApp->id, ClientApp::class, 'New application created: ' . $ClientApp->name);

            return redirect("/applications");
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while trying to create a new application.');
        }
    }

    public function showUpdate(Request $request, $id)
    {
        try {
            $client = ClientApp::find($id);
            return view('showUpdateApp', ["application" => $client]);
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while trying to display the update form.');
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $ClientApp = ClientApp::findOrFail($id);
            $previousName = $ClientApp->name;
            $ClientApp->name = $request->input('name');
            $ClientApp->save();
            $this->logMessage($ClientApp->id, ClientApp::class, "Application updated: name changed from $previousName to " . $ClientApp->name);

            return redirect('/applications');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while trying to update the application.');
        }
    }

    public function delete(Request $request, $id)
    {
        try {
            $ClientApp = ClientApp::findOrFail($id);
            $clientName = $ClientApp->name;
            ClientApp::destroy($id);
            $this->logMessage($id, ClientApp::class, "Application deleted: $clientName");

            return redirect()->route('applications.index');
        } catch (\Exception $e) {
            return back()->with('error', 'An error occurred while trying to delete the application.');
        }
    }
}

