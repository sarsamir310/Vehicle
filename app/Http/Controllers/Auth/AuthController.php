<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;


class AuthController extends Controller
{
    public function login(Request $request)
    {
        // Validate input
        $request->validate([
            'email' => 'required|email', // Ensure email format is correct
            'password' => 'required|min:6', // Ensure password meets min length requirements
        ]);

        // Map input fields to credentials (use lowercase 'email' and 'password')
        $credentials = [
            'email' => $request->email,  // Custom 'Email' column
            'password' => $request->password,  // Custom 'Password' column
        ];

        // Check if credentials are correct
        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            // Generate the authentication token
            $token = $user->createToken('authToken')->plainTextToken;

            return response()->json([
                'message' => 'Login successful!',
                'user' => $user,
                'token' => $token,
            ], 200);
        }

        // If credentials are incorrect, return an error message
        return response()->json(['message' => 'Email or password is incorrect.'], 401);
    }
 public function register(Request $request) {
    $validated = Validator::make($request->all(), [
        'full_name' => 'required|max:191',
        'email' => 'required|max:191|unique:users',
        'password' => 'required|confirmed|min:6',

    ], [
        'full_name.required' => 'This field is required.',
        'email.required' => 'email is required.',
        'email.unique' => 'This email has already been taken.',
        'password.required' => 'password is required.',
        'password.confirmed' => 'The password confirmation does not match.',
        'password.min' => 'Password must be at least 6 characters.',
    ]);

    if ($validated->fails()) {
        return response()->json([
            'status' => 'error',
            'errors' => $validated->errors(),
        ], 422);
    }

    $data = User::create([
        'full_name' => $request->full_name,
        'email' => $request->email,
        'password' => bcrypt($request->password),
        'api_token' => Str::random(60),
    ]);

    return response()->json([
        'status' => 'success',
        'data' => $data,
    ], 201);
}

public function logout()
{
    if(auth()->user()) {
        $user = auth()->user();
        $user->api_token = null;
        $user->save();
        return response()->json(['message' => 'thank you for using our app']);
    }
    return response()->json(['error' => 'fail',
        'status' => 401,
    ]);
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
    public function sendResetLinkEmail(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $status = Password::sendResetLink($request->only('email'));

        return $status === Password::RESET_LINK_SENT
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['message' => __($status)], 400);
    }

    // Reset password
    public function reset(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|confirmed|min:8',
            'token' => 'required',
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $status = Password::reset(
            $request->only('email', 'password', 'password_confirmation', 'token'),
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password),
                ])->save();
            }
        );
        return $status === Password::PASSWORD_RESET
            ? response()->json(['message' => __($status)], 200)
            : response()->json(['message' => __($status)], 400);
    }

    }
