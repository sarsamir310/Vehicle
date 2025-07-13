<?php

namespace App\Http\Controllers;


use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;


class DrivingModeController extends Controller
{
    public function toggleDrivingMode(Request $request)
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->is_driving_mode = $request->input('is_driving_mode', false);
        $user->save();

        return response()->json([
            'message' => 'Driving mode updated successfully',
            'is_driving_mode' => $user->is_driving_mode
        ]);
    }

    // Save selected distracting apps
    public function updateDistractingApps(Request $request)
    {
        $request->validate([
            'distracting_apps' => 'required|array'
        ]);

        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        $user->distracting_apps = $request->input('distracting_apps');
        $user->save();

        return response()->json([
            'message' => 'Distracting apps updated successfully',
            'distracting_apps' => $user->distracting_apps
        ]);
    }

    // Get driving mode status and selected apps
    public function getDrivingMode()
    {
        $user = Auth::user();

        if (!$user) {
            return response()->json(['error' => 'User not found'], 404);
        }

        return response()->json([
            'is_driving_mode' => $user->is_driving_mode,
            'distracting_apps' => $user->distracting_apps ?? []
        ]);
    }
}
