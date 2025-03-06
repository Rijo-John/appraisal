<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\AppraisalCycle;

class AdministrationController extends Controller
{
    public function index(Request $request){
        
        return view('administration');
    }
}
