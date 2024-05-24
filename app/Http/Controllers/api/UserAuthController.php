<?php

namespace App\Http\Controllers\api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class UserAuthController extends Controller
{
      
     //register - creating ur account 
     //account number generation 
     private function generateUniqueAccountNumber() {
        do {
            // Generate a random 10-digit account number
            $accountNumber = mt_rand(100000000000, 999999999999);
        } while (User::where('account_number', $accountNumber)->exists());

        return $accountNumber;
    }
    
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
            // Generate unique account number
            $accountNumber = $this->generateUniqueAccountNumber();
            // return $accountNumber;

            // Create user
            $user = User::create([
                'name' => $request->name,
                'email' => $request->email,
                'password' => Hash::make($request->password),
                'phone' => $request->phone,
                'account_number' => $accountNumber, // Add this line to store the account number
                'balance' => 0.00, // Default balance,
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

  // send money - list view 
  public function listUsers()
  {
      // Get the authenticated user
      $authenticatedUser = Auth::user();

      // Fetch users from the database excluding the authenticated user
      $users = User::select('name', 'phone', 'account_number', 'image')
                  ->where('id', '!=', $authenticatedUser->id)
                  ->get();

      // Return the user data as a JSON response
      return response()->json([
          'users' => $users
      ], 200); // 200 OK status code
  }

// update the image 
public function updateProfileImage(Request $request)
{
    $user = Auth::user();

    // Validate the request
    $validation = Validator::make($request->all(), [
        'image' => 'required|image|max:2048', // max 2MB
    ]);

    // Check if validation fails
    if ($validation->fails()) {
        return response()->json([
            'errors' => $validation->errors()
        ], 422);
    }

    // Handle the image upload
    if ($request->hasFile('image')) {
        // Get the image from the request
        $getImage = $request->file('image');
        $imageName = time() . '.' . $getImage->getClientOriginalExtension();
        $imagePath = 'public/profile_image' . $imageName;

        // Delete the old image if it exists
        if ($user->image) {
            Storage::disk('public')->delete($user->image);
        }

        // Move the new image to the public storage
        $getImage->storeAs('public/profile_image', $imageName, 'public');

        // Update the user's profile image path
        $user->image = $imagePath;
        $user->save();

        return response()->json([
            'message' => 'Profile image updated successfully',
            'image' => $imagePath
        ], 200);
    }

    return response()->json([
        'message' => 'No image uploaded'
    ], 400);
}
}
