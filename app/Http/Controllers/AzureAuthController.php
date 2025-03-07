<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Laravel\Socialite\Facades\Socialite;
use App\Models\InternalUser;
use App\Models\AppraisalCycle;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\DB;



class AzureAuthController extends Controller
{
    // Redirect to Microsoft Azure Login
    public function redirectToAzure()
    {
         return Socialite::driver('microsoft')->redirect();
    }

    public function getAppraisalFormData($headsId){
        //dd($headsId);
        $appraisalFormData = DB::table('appraisal_form')
        ->select('id', 'appraiser_officer_heads_id','appraiser_officer_name','reporting_officer_name')
        ->where('employee_heads_id', $headsId)
          ->where('status', 1)
          ->first();
          //dd($appraisalFormData);

          return $appraisalFormData;

    }

    

    // Handle Azure Callback
    public function handleAzureCallback()
    {
        $azureUser = Socialite::driver('microsoft')->stateless()->user();
        $user = InternalUser::where('email', $azureUser->getEmail())
                        ->where('status', 'Active')
                        ->first();
                        //dd($user['heads_id']);
        if ($user) {

            session(['logged_user_heads_id' => $user['heads_id']]);
            session(['logged_user_designation_id' => $user['designation_id']]);
            $appraisalFormData = $this->getAppraisalFormData($user['heads_id']);
            $currentAppraisalCycleData = $this->getCurrentAppraisalCycle();
            $appraiserOfficerHeadsId = ($appraisalFormData && $appraisalFormData->appraiser_officer_heads_id)?$appraisalFormData->appraiser_officer_heads_id:0;
            $appraiserOfficerName = ($appraisalFormData && $appraisalFormData->appraiser_officer_name)?$appraisalFormData->appraiser_officer_name:'';
            $appraisalFormId = ($appraisalFormData && $appraisalFormData->id)?$appraisalFormData->id:0;

            //dd($appraisalFormData);
            session(['appraiser_officer_heads_id' => $appraiserOfficerHeadsId]);
            session(['appraisal_form_id' => $appraisalFormId]);
            session(['current_appraisal_cycle' => $currentAppraisalCycleData->id]);
            session(['appraiserOfficerName' => $appraiserOfficerName]);
            // If user exists, log them in
            //Auth::login($user);
            Auth::guard('web')->login($user);
            //return redirect()->route('dashboard')->with('success', 'Successfully logged in!');
            return redirect()->route('myappraisal')->with('success', 'Successfully logged in!');
        } else {
            // If user does not exist, show an error
            return response()->view('errors.unauthorized', ['email' => $azureUser->getEmail()], 403);
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
