<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Auth\Events\PasswordReset;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Cache;


class AuthController extends Controller
{
public function login(Request $request)
{


    if(auth()->attempt(['email'=>$request->input('email'),
        'password'=>$request->input('password')]))
    {// if correct
        $user = auth()->user();
        $user->api_token=Str::random(60);
        $user->save();
        return $user;
    }
    return response('password or email is incorrect',400);

}

public function register(Request $request)
{
    $validated = Validator::make($request->all(), [
        'Full_name' => 'required|max:191',
        'Email' => 'required|max:191|unique:users',
        'Password' => 'required|confirmed|min:6',],
        ['Full_name.required'=>'this field is required.']);
    if ($validated->fails()) {
        return $validated->errors();

    } else {
        $data = User::create([
            'Full_name' => $request->Full_name,
            'Email' => $request->Email,
            'Password' => bcrypt($request->Password),
            'api_token' => Str::random(60),
        ]);
        return $data;
    }
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
            'Email' => 'required|string', // or 'email' if you prefer
        ]);

// Generate a 5-digit OTP
        $otp = mt_rand(10000, 99999);

// Store OTP in cache for 5 minutes
        Cache::put($request->Email, $otp, 300); // 300 seconds = 5 minutes

// Send OTP via SMS or email here
// Example: Mail::to($request->email)->send(new OtpMail($otp));

        return response()->json(['message' => 'OTP has been sent.', 'otp' => $otp]);
    }

// Validate OTP
    public function validateOtp(Request $request)
    {
// Validate incoming request
        $request->validate([
            'Email' => 'required|string',
            'otp' => 'required|digits:5', // Ensure OTP is 5 digits
        ]);

// Retrieve the OTP from the cache
        $cachedOtp = Cache::get($request->Email);

        if ($cachedOtp && $cachedOtp == $request->otp) {
// OTP is valid
            Cache::forget($request->Email); // Remove OTP from cache
            return response()->json(['message' => 'OTP is valid.']);
        }

        return response()->json(['message' => 'Invalid OTP or OTP expired.'], 400);
    }
    public function sendResetLink(Request $request)
    {
        $request->validate(['Email' => 'required|email']);

        $response = Password::sendResetLink($request->only('Email'));

        return $response == Password::RESET_LINK_SENT
            ? response()->json(['status' => trans($response)], 200)
            : response()->json(['Email' => trans($response)], 400);
    }

    // Reset the password
    public function reset(Request $request)
    {
        $request->validate([
            'Email' => 'required|email',
            'token' => 'required',
            'Password' => 'required|confirmed|min:8',
        ]);

        $user = User::where('email', $request->Email)->first();

        if (!$user) {
            return response()->json(['error' => 'User not found.'], 404);
        }

        $response = Password::reset(
            $request->only('Email', 'Password', 'Password_Confirmation', 'token'),
            function ($user, $password) {
                $user->Password = bcrypt($password);
                $user->save();
                event(new PasswordReset($user));
            }
        );

        return $response == Password::PASSWORD_RESET
            ? response()->json(['status' => trans($response)], 200)
            : response()->json(['error' => trans($response)], 400);
    }
}
