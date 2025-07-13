<?php

namespace App\Http\Controllers;
use App\Models\EmergencyOverride;
use App\Models\Violation;
use Illuminate\Http\Request;
use Illuminate\Support\Carbon;

class EmergencyController extends Controller
{
    public function createEmergencyRequest(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'vehicle_id' => 'required|exists:vehicles,id',
        ]);

        $emergencyRequest = EmergencyOverride::create([
            'user_id' => $request->user_id,
            'vehicle_id' => $request->vehicle_id,
            'expires_at' => Carbon::now()->addSeconds(30),
        ]);

        return response()->json(['message' => 'Emergency request created', 'data' => $emergencyRequest], 201);
    }
    public function uploadEvidence(Request $request)
    {
        $request->validate([
            'emergency_id' => 'required|exists:emergency_overrides,id',
            'category' => 'required|string',
            'evidence' => 'required|file|mimes:jpg,png,pdf|max:2048',
        ]);

        $emergency = EmergencyOverride::find($request->emergency_id);


        if (Carbon::now()->greaterThan($emergency->expires_at)) {
            return response()->json(['message' => 'The time to upload evidence has expired'], 403);
        }


        $path = $request->file('evidence')->store('evidences', 'public');


        $emergency->update([
            'category' => $request->category,
            'evidence' => $path,
            'status' => 'pending',
        ]);

        return response()->json(['message' => 'Evidence uploaded successfully', 'data' => $emergency], 200);
    }

    public function checkForViolations()
    {

        $expiredRequests = EmergencyOverride::whereNull('evidence')
            ->where('expires_at', '<', Carbon::now())
            ->get();


        if ($expiredRequests->isEmpty()) {
            return response()->json(['message' => 'No expired requests found, no violations created']);
        }

        foreach ($expiredRequests as $request) {
            $existingViolation = Violation::where('user_id', $request->user_id)
                ->where('vehicle_id', $request->vehicle_id)
                ->where('violation_type', 'speeding')
                ->exists();
            if (!$existingViolation) {

                Violation::create([
                    'user_id' => $request->user_id,
                    'vehicle_id' => $request->vehicle_id,
                    'violation_type' => 'speeding',
                    'evidence_status' => 'pending'
                ]);
            }
            $request->update(['status' => (string) 'expired']);
        }

        return response()->json(['message' => 'Expired requests found and violations created']);
    }

}
