<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\InternalUser;
use Illuminate\Support\Facades\Auth;


class AzureAuthController extends Controller
{
    // Redirect to Microsoft Azure Login
    public function redirectToAzure()
    {
         return Socialite::driver('microsoft')->redirect();
    }

    // Handle Azure Callback
    public function handleAzureCallback()
    {
        $azureUser = Socialite::driver('microsoft')->stateless()->user();
        $user = InternalUser::where('email', $azureUser->getEmail())->first();
        if ($user) {
            // If user exists, log them in
            //Auth::login($user);
            Auth::guard('web')->login($user);


            return redirect()->route('dashboard')->with('success', 'Successfully logged in!');
        } else {
            // If user does not exist, show an error
            return redirect('/login')->with('error', 'Unauthorized: You are not registered in our system.');
        }

        
    }

    // Logout
    public function logout(Request $request)
    {
        Auth::guard('web')->logout(); // Logout the user

        // Invalidate session and regenerate CSRF token for security
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Successfully logged out!');
    }
}
