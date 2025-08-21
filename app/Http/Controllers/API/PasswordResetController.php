<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Log;
use App\Mail\PasswordResetMail; // Add this line
use App\Models\Business;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Mail; // Add this line
use Symfony\Component\HttpFoundation\Response;
class PasswordResetController extends Controller
{
    // public function sendResetLinkEmail(Request $request)
    // {
    //     // Validate the request
    //     $validator = Validator::make($request->all(), [
    //         'email' => 'required|email',
    //     ]);

    //     if ($validator->fails()) {
    //         return response()->json(['error' => $validator->errors()], 422);
    //     }

    //     // Generate a token for password reset
    //     $token = Password::createToken(User::where('email', $request->email)->first());

    //     // Send the password reset email
    //     Mail::to($request->email)->send(new PasswordResetMail($token));

    //     // Return a success response
    //     return response()->json(['message' => 'Password reset link sent successfully'], 200);
    // }
    public function sendResetLinkEmail(Request $request)
{
    // Validate the request
    $validator = Validator::make($request->all(), [
        'email' => 'required|email',
    ]);

    if ($validator->fails()) {
        return response()->json(['error' => $validator->errors()], 422);
    }

    // Find the user by email
    $user = User::where('email', $request->email)->first();

    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    // Generate a token for password reset
    $token = Password::createToken($user);

    // Prepare data to send to the external API
    $data = [
        'email' => $user->email,
        'token' => $token,
    ];

    // Send data to the specified API endpoint
    $response = Http::post('https://hook.eu2.make.com/jgfhqap3e0zwpnp9p8m6oncg4ejmu2bt', $data);

    // Check if the API request was successful
    if ($response->successful()) {
        return response()->json(['message' => 'Password reset link sent successfully'], 200);
    } else {
        return response()->json(['error' => 'Failed to send reset link'], 500);
    }
}


    public function resetPassword(Request $request, $token)
    {
        // Validate the request
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required|string|confirmed|min:8',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }
    
        // Reset the password
        $status = Password::reset(
            [
                'email' => $request->email,
                'password' => $request->password,
                'password_confirmation' => $request->password_confirmation,
                'token' => $token, // Use the token from the URL
            ],
            function ($user, $password) {
                $user->forceFill([
                    'password' => Hash::make($password)
                ])->save();
            }
        );
    
        // Return the appropriate response
        if ($status === Password::PASSWORD_RESET) {
            return response()->json(['message' => __($status)], 200);
        } else {
            return response()->json(['error' => __($status)], 400);
        }
    }
    private function getValidatedBusinessId(Request $request)
    {
        $businessId = $request->header('business_id');
        
       
        if (!$businessId) {
            abort(Response::HTTP_UNPROCESSABLE_ENTITY, 'Missing business_id header');
        }
        
      
        $business = Business::where('id', $businessId)
            ->where('user_id', Auth::id())
            ->first();

        if (!$business) {
            abort(Response::HTTP_FORBIDDEN, 'Unauthorized access to business');
        }

        return $businessId;
    }
}