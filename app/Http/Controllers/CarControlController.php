<?php

namespace App\Http\Controllers;

use App\Models\Log;
use Illuminate\Http\Request;

class CarControlController extends Controller
{
    public function issueWarning(Request $request) {
        $vehicle = auth()->user()->vehicles->first();
        $currentLog = Log::where('vehicle_id', $vehicle->id)->latest()->first();

        if ($currentLog->current_speed > $currentLog->detected_speed_limit) {
            // Trigger warning logic here
            return response()->json(['message' => 'Warning issued to reduce speed!']);
        }

        return response()->json(['message' => 'Speed is within the limit.']);
    }
}
