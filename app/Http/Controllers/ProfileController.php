<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;

class ProfileController extends Controller
{
    // Display the user's profile
    public function show()
    {
        $user = Auth::user();
        return response()->json($user, 200);
    }

//     // Update the user's profile
//     public function update(Request $request)
//     {
//         $user = Auth::user();

//         // Validate the incoming request data
//         $request->validate([
//             'name' => 'required|string|max:255',
//             'email' => 'required|string|email|max:255|unique:users,email,' . $user->id,
//             'phone_number' => 'required|string|max:15|unique:users,phone_number,' . $user->id,
//             'gender' => 'nullable|string|in:male,female,other',
//         ]);

//         // Update the user's profile
//         $user->update($request->only(['name', 'email', 'phone_number', 'gender']));

//         return response()->json(['message' => 'Profile updated successfully', 'user' => $user], 200);
//     }
// 
}