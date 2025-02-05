<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

use App\Models\User;
use App\Models\Connections;

class UserController extends Controller
{
    public function fetch_allUsers() {
        $current_user = Auth::user();
        $current_user_id = $current_user? $current_user->id : null;
        if (! $current_user_id) {
            return;
        }

        $users = User::query()->when($current_user_id, function ($query) use ($current_user_id){
            return $query->where('id', '!=', $current_user_id)->get();
        });

        return view('users.allUsers', compact('users', 'current_user'));
    }

    public function send_connection(Request $request, $user_id) {
        $current_user = Auth::user();
        $current_user_id = $current_user? $current_user->id : null;
        if (! $current_user_id) {
            return;
        }
    }

    public function manageProfile() {
        $user = Auth::user();
        return view('users.manageProfile', compact('user'));
    }

    public function updateProfile(Request $request) {
        // Validate the input data
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:chat_users,email,' . Auth::id(),
        ]);

        // Get the currently authenticated user
        $user = Auth::user();

        // Update the user's profile information
        $user->update([
            'first_name' => $request->input('first_name'),
            'last_name' => $request->input('last_name'),
            'email' => $request->input('email'),
        ]);

        // Save the user with the updated profile picture (if any)
        $user->save();

        // Redirect back with a success message
        return back()->with('success', 'Profile updated successfully!');
    }

    public function update_profile_pic(Request $request) {
        // Validate the input data
        $request->validate([
            'profile_picture' => 'required|image|mimes:jpg,jpeg,png,gif|max:2048', // Add validation for profile picture
        ]);
    
        $user = Auth::user();
    
        if ($request->hasFile('profile_picture')) {
            // Delete the old profile picture if it exists
            if ($user->profile_picture && file_exists(public_path('uploads/profile_pictures/' . $user->profile_picture))) {
                unlink(public_path('uploads/profile_pictures/' . $user->profile_picture));
            }
    
            // Generate a unique file name
            $fileName = uniqid() . '.' . $request->file('profile_picture')->getClientOriginalExtension();
    
            // Move the new profile picture to the specified path
            $request->file('profile_picture')->move(public_path('uploads/profile_pictures'), $fileName);
    
            // Update the user's profile picture path
            $user->profile_picture = 'uploads/profile_pictures/' . $fileName;
            $user->save();
    
            // Return a success message
            return redirect()->back()->with('success', 'Profile picture updated successfully.');
        }
    
        // If no file was uploaded, return with a message
        return redirect()->back()->with('error', 'No profile picture uploaded.');
    }
    

    public function searchUsers(Request $request) {
        // Validate the input
        $request->validate([
            'query' => 'required|string|max:255',
        ]);
    
        $query = $request->input('query');
    
        // Fetch matching users
        $users = User::where('first_name', 'LIKE', "%{$query}%")
            ->orWhere('last_name', 'LIKE', "%{$query}%")
            ->limit(10)
            ->orderBy('first_name') // Optional: Order by first name
            ->get(['id', 'first_name', 'last_name', 'profile_picture']);
    
        // Return results or empty message
        if ($users->isEmpty()) {
            return response()->json(['message' => 'No users found'], 200);
        }
    
        return response()->json($users);
    }
}
