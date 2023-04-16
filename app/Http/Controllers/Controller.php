<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    
    function logMessage($loggableId, $loggableType, $message) {
        try {
            Log::create([
                'loggable_id' => $loggableId,
                'loggable_type' => $loggableType,
                'message' => $message
            ]);
        } catch (\Exception $e) {
            return redirect()->back()->with('message', [
                'type' => 'error',
                'text' => $e->getMessage()
            ]);
        }
    }    

}
