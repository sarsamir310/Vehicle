<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\Vehicle;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;


class AuthController extends Controller
{
    // ✅ Register - Step 1 (Basic User Details)
    public function registerStep1(Request $request)

    {
        $validator = Validator::make($request->all(), [
            'full_name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email',
            'phone' => 'required|string|max:15',
            'password' => 'required|string|min:6',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::create([
            'full_name' => $request->full_name,
            'email' => $request->email,
            'phone'=>$request->phone,
            'password' => Hash::make($request->password),
        ]);

        return response()->json([
            'message' => 'User registered successfully. Proceed to step 2.',
            'user' => $user
        ], 201);
    }
    public function registerStep2(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'user_id' => 'required|exists:users,id',
            'national_id' => 'required|string|max:20',
            'address' => 'required|string|max:255',
            'license_number' => 'required|string|max:50',
            'vehicle_model' => 'required|string|max:100',
            'license_plate' => 'required|string|max:20|unique:vehicles,license_plate',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = User::findOrFail($request->user_id);
        $user->update($request->only(['phone', 'national_id', 'address','license_number','driving_license']));

        $vehicle = Vehicle::create([
            'user_id' => $user->id,
            'vehicle_model' => $request->vehicle_model,
            'license_plate' => $request->license_plate,
        ]);

        return response()->json([
            'message' => 'User profile and vehicle added successfully!',
            'user' => $user,
            'vehicle' => $vehicle
        ], 201);
    }

    public function generateOtp(Request $request)
    {
// Validate incoming request
        $request->validate([
            'email' => 'required|string', // or 'email' if you prefer
        ]);

// Generate a 5-digit OTP
        $otp = mt_rand(10000, 99999);

// Store OTP in cache for 5 minutes
        Cache::put($request->email, $otp, 300); // 300 seconds = 5 minutes

// Send OTP via SMS or email here
// Example: Mail::to($request->email)->send(new OtpMail($otp));

        return response()->json(['message' => 'OTP has been sent.', 'otp' => $otp]);
    }

// Validate OTP
    public function validateOtp(Request $request)
    {
// Validate incoming request
        $request->validate([
            'email' => 'required|string',
            'otp' => 'required|digits:5', // Ensure OTP is 5 digits
        ]);

// Retrieve the OTP from the cache
        $cachedOtp = Cache::get($request->email);

        if ($cachedOtp && $cachedOtp == $request->otp) {
// OTP is valid
            Cache::forget($request->email); // Remove OTP from cache
            return response()->json(['message' => 'OTP is valid.']);
        }

        return response()->json(['message' => 'Invalid OTP or OTP expired.'], 400);
    }
    // ✅ Login
    public function login(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email_or_national_id' => 'required|string',
            'password' => 'required|string',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $user = User::where('email', $request->email_or_national_id)
            ->orWhere('national_id', $request->email_or_national_id)
            ->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json(['error' => 'Invalid credentials'], 401);
        }

        // Generate Sanctum Token
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'message' => 'Login successful',
            'user' => $user,
            'number_of_cars' => $user->vehicles()->count(),
            'token' => $token,
        ]);
    }

    // ✅ Logout
    public function logout(Request $request)
    {
        $request->user()->tokens()->delete();

        return response()->json([
            'message' => 'Logged out successfully'
        ]);
    }

}
