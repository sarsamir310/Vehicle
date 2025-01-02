<?php
namespace App\Http\Controllers;
use App\Models\Log;
use Illuminate\Http\Request;
use App\Models\Violation;
use App\Models\EmergencyOverride;
use App\Models\DriverMonitoring;
use App\Models\Notification;

class LogController extends Controller
{
    public function fetchLogs(Request $request)
    {
        $vehicle = auth()->user()->vehicles->first();
        $logs = Log::where('vehicle_id', $vehicle->id)->orderBy('created_at', 'desc')->get();
        return response()->json(['logs' => $logs]);
    }

    public function addLog(Request $request) {
        $vehicle = auth()->user()->vehicles->first();
        $log = Log::create([
            'vehicle_id' => $vehicle->id,
            'location' => $request->input('location'),
            'current_speed' => $request->input('current_speed'),
            'detected_speed_limit' => $request->input('detected_speed_limit'),
            'description' => $request->input('description'),
            'alert_issued' => $request->input('alert_issued', false),
        ]);
        return response()->json(['log' => $log]);
    }
}





































































