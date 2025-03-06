<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\AppraisalCycle;

class DashboardController extends Controller
{
   
    public function index(Request $request){
        $sessionData = session()->all();
        $appraiserOfficerName = $sessionData['appraiserOfficerName'];
        $appraisalData = AppraisalCycle::select('appraisal_period')->where('status',1)->first();
        $user = Auth::user();
        
        $user->name = $user->first_name . ' ' . $user->last_name;
        return view('dashboard', compact('user','appraisalData','appraiserOfficerName'));
    }
}
