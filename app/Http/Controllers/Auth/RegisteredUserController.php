<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Providers\RouteServiceProvider;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('users.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'first_name' => ['required', 'string', 'max:255'],
            'last_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);
    
        // Check if the email exists in the database
        $existingUser = User::where('email', $request->email)->first();
    
        if ($existingUser) {
            if (in_array($existingUser->status, ['0', '1'])) {
                // Prevent registration if the status is 0 or 1
                return redirect()->back()->withErrors([
                    'email' => 'Email is already in use...'
                ]);

            }
    
            if ($existingUser->status == '5') {
                // If status is 5, delete the old record to allow re-registration
                $existingUser->delete();
            }
        }
    
        // Proceed with registration
        $user = User::create([
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);
    
        // Fire the Registered event
        event(new Registered($user));
    
        // Log the user in
        Auth::login($user);
    
        return redirect()->route('users.all')->with('success', 'Signed-up successfully!');
    }
}
