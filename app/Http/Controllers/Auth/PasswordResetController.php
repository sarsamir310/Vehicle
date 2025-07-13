<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Notifications\ResetPasswordNotification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\URL;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB; 
use App\Models\User;



 class PasswordResetController extends Controller
 {
// {
//     public function sendResetLinkEmail(Request $request)
//     {
//         $request->validate(['email' => 'required|email']);

//         $status = Password::sendResetLink(
//             $request->only('email'),
//             function ($user, $token) {
//                 $resetUrl = URL::to("api/reset-password?token=$token&email=$user->email");
//                 $user->notify(new ResetPasswordNotification($resetUrl));
//             }
//         );

//         return $status === Password::RESET_LINK_SENT
//             ? response()->json(['message' => 'Reset link sent successfully.'], 200)
//             : response()->json(['message' => 'Unable to send reset link.'], 400);
//     }

//     // Reset password
//     public function reset(Request $request): JsonResponse
//     {
//         $validator = Validator::make($request->all(), [
//             'email' => 'required|email',
//             'password' => 'required|confirmed|min:8',
//             'token' => 'required',
//         ]);

//         if ($validator->fails()) {
//             return response()->json(['errors' => $validator->errors()], 422);
//         }

//         $status = Password::reset(
//             $request->only('email', 'password', 'password_confirmation', 'token'),
//             function ($user, $password) {
//                 $user->forceFill([
//                     'password' => Hash::make($password),
//                 ])->save();
//             }
//         );

//         return $status === Password::PASSWORD_RESET
//             ? response()->json(['message' => __($status)], 200)
//             : response()->json(['message' => __($status)], 400);
//     }
// }
public function sendOtp(Request $request)
{
    $request->validate(['email' => 'required|email|exists:users,email']);

    $code = rand(100000, 999999);

    // Save code in password_resets table
    DB::table('password_resets')->updateOrInsert(
        ['email' => $request->email],
        ['token' => $code, 'created_at' => now()]
    );

    // ✅ Return the code in response (for testing)
    return response()->json([
        'message' => 'OTP sent (debug mode)',
        'otp' => $code // you see the OTP directly
    ]);
}
//////////////////////////////////////////////
public function verifyOtp(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required'
    ]);

    $reset = DB::table('password_resets')
                ->where('email', $request->email)
                ->where('token', $request->otp)
                ->first();

    if (!$reset) {
        return response()->json(['message' => 'Invalid OTP'], 400);
    }

    return response()->json(['message' => 'OTP verified']);
}
/////////////////////////////////////////////////////
public function resetPassword(Request $request)
{
    $request->validate([
        'email' => 'required|email',
        'otp' => 'required',
        'password' => 'required|confirmed',
    ]);

    $reset = DB::table('password_resets')
                ->where('email', $request->email)
                ->where('token', $request->otp)
                ->first();

    if (!$reset) {
        return response()->json(['message' => 'Invalid OTP or email'], 400);
    }

    User::where('email', $request->email)->update([
        'password' => Hash::make($request->password),
    ]);

    DB::table('password_resets')->where('email', $request->email)->delete();

    return response()->json(['message' => 'Password changed successfully']);
}

 }