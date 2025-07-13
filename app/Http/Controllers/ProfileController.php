<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\Vehicle;
use App\Http\Controllers\Controller;

class ProfileController extends Controller
{
    public function update(Request $request)
    {
        $user = auth()->user();

        // Validation
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone' => 'required|string|max:20',
            'job_title' => 'nullable|string|max:255',
            'address' => 'nullable|string|max:255',
            'national_id' => 'nullable|string|max:20|unique:users,national_id,' . $user->id,
            'driving_license' => 'nullable|file|mimes:jpg,png,pdf|max:2048',
            'vehicle_model' => 'nullable|string|max:255',
        ]);

        // Update profile data
        $user->full_name = $request->full_name;
        $user->phone = $request->phone;
        $user->job_title = $request->job_title;
        $user->address = $request->address;
        $user->national_id = $request->national_id;

        // Handle file upload (Driving License)
        if ($request->hasFile('driving_license')) {
            // Delete old file if exists
            if ($user->driving_license) {
                Storage::delete($user->driving_license);
            }
            // Store in 'public' disk so it's accessible via URL
            $user->driving_license = $request->file('driving_license')->store('driving_licenses', 'public');
        }

        $user->save();

        // **Step 5: Update Vehicle Type**
        if ($request->vehicle_model) {
            $vehicle = $user->vehicles()->first(); // Get the first vehicle
            if ($vehicle) {
                $vehicle->vehicle_model = $request->vehicle_model;
                $vehicle->save();
            } else {
                // Optionally, create a new vehicle entry if no vehicle exists
                $user->vehicles()->create(['model' => $request->vehicle_model]);
            }
        }

        return response()->json([
            'message' => 'Profile updated successfully',
            'user' => $user->load('vehicles'), // Load vehicles in response
        ], 200);
    }
    public function show()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        // Load only vehicle_model from vehicles
        $user->load(['vehicles' => function ($query) {
            $query->select('id', 'user_id', 'vehicle_model');
        }]);

        return response()->json($user);
    }
}
