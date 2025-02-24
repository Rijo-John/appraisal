<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\InternalUser;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;



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

    public function logout(Request $request)
    {
        // Logout user from Laravel
        Auth::guard('web')->logout();

        // Destroy the session
        $request->session()->invalidate();
        $request->session()->flush();
        $request->session()->regenerateToken();

        // Forget session cookies
        Cookie::queue(Cookie::forget('laravel_session'));
        Cookie::queue(Cookie::forget('XSRF-TOKEN'));
        Cookie::queue(Cookie::forget(config('session.cookie')));

        // Force Microsoft to sign out all accounts globally
        $microsoftLogoutUrl = 'https://login.microsoftonline.com/common/oauth2/logout?post_logout_redirect_uri=' . urlencode(url('/login')) . '&federated=1';

        return redirect($microsoftLogoutUrl);
    }


    // Logout
    /*public function logout(Request $request)
    {
        // Logout user from Laravel
        Auth::guard('web')->logout();
        //Session::flush();
        $request->session()->invalidate();
        $request->session()->flush();
        $request->session()->regenerateToken();

        // Forget session cookies
        Cookie::queue(Cookie::forget('laravel_session'));
        Cookie::queue(Cookie::forget('XSRF-TOKEN'));
        Cookie::queue(Cookie::forget(config('session.cookie'))); // Clears default session cookie

        // Logout from Microsoft Azure
        $microsoftLogoutUrl = 'https://login.microsoftonline.com/' . env('AZURE_TENANT_ID') . '/oauth2/v2.0/logout?post_logout_redirect_uri=' . urlencode(url('/login'));

        return redirect($microsoftLogoutUrl);
    }*/



}
