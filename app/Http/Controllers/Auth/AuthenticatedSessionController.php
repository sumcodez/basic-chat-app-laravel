<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Http\Requests\Auth\LoginRequest;
use App\Providers\RouteServiceProvider;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\View\View;

class AuthenticatedSessionController extends Controller
{
    /**
     * Display the login view.
     */
    public function create(): View
    {
        return view('users.login');
    }

    /**
     * Handle an incoming authentication request.
     */
    public function store(LoginRequest $request): RedirectResponse
    {
        try {
            // Attempt authentication
            $request->authenticate();
    
            // Get the authenticated user
            $user = $request->user();
    
            // Check if the user's account is deactivated
            if ($user && $user->status == '5') {
                // Log the user out immediately
                Auth::guard('web')->logout();
    
                // Invalidate the session
                $request->session()->invalidate();
                $request->session()->regenerateToken();
    
                // Redirect back with an error message
                return redirect()->back()->withErrors([
                    'email' => 'Your account has been deactivated. Please contact support for assistance.'
                ]);
            }
    
            // Set the status to 0 for the authenticated user
            if ($user) {
                $user->update([
                    'status' => '0' // Ensure the value matches your ENUM type
                ]);
            }
    
            // Regenerate session to prevent session fixation
            $request->session()->regenerate();
    
            // Flash success message
            session()->flash('success', 'Logged in successfully.');
    
            // Redirect based on user role
            if ($user->is_admin == 1) {
                Auth::guard('web')->logout();
                //return redirect()->route('admin.dashboard'); // Redirect to admin dashboard
    
                // Redirect back with an error message
                return redirect()->back()->withErrors([
                    'email' => 'You are not an admin. Please use admin login'
                ]);
            }
    
            return redirect()->route('users.all'); // Redirect to events page
        } catch (\Illuminate\Validation\ValidationException $e) {
            // Flash error message for invalid credentials
            return redirect()->back()
                ->withErrors(['email' => 'The provided credentials are wrong']);
        }
    }

    /**
     * Destroy an authenticated session.
     */
    public function destroy(Request $request): RedirectResponse
    {
        $user = Auth::user();
        if ($user) {
            $user->update([
                'status' => '1' // Ensure status is a string if it's an ENUM column
            ]);
        }
        
        Auth::guard('web')->logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/');
    }
}
