<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class DashboardController extends Controller
{
   
    public function index(Request $request){
        $sessionData = session()->all();
        
        //dd($sessionData);
        return view('dashboard');
    }
}
