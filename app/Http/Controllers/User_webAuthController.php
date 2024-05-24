<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
class User_webAuthController extends Controller
{
     //register - creating ur account 
     public function register(Request $request)
     {
        $validation = Validator::make($request->all(), [
            'name' => 'required|string',
            'email' => 'required|string|email|unique:users,email',
            'password' => 'required|min:8',
            'phone' => 'required|unique:users,phone',
        ]);
    
        // Check if validation fails
        if ($validation->fails()) {
            return response()->json([
                'errors' => $validation->errors()
            ], 422);
        } else {
            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,  // Add this line to store phone
            ]);
    
            // Return response
            return response()->json([
                'message' => 'User Created',
                'user' => $user
            ], 201);
        }
    }
                   
    
    
    
    // login  - 
    public function login(Request $request)
{
    // Validate the incoming request data
    $validation = Validator::make($request->all(), [
        'email' => 'required|string|email',
        'password' => 'required|min:8'
    ]);

    // Check if validation fails
    if ($validation->fails()) {
        return response()->json([
            'errors' => $validation->errors()
        ], 422);
    } else {
        // Attempt to find the user by email
        $user = User::where('email', $request->email)->first();

        // Check if user exists and if the password is correct
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'message' => 'Invalid Credentials'
            ], 401);
        }

        // Create a new token for the user
        $token = $user->createToken($user->name . '-AuthToken')->plainTextToken;

        // Return response with the access token
        return response()->json([
            'access_token' => $token,
            'message' => ' sucessfully login  ',
            'user' => $user
        ]);
    }
}
    
    
    // logout 
    public function logout(Request $request){
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            "message" => "Logged out"
        ]);
    }

}
