<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;
use Illuminate\Http\Request;

class ListController extends Controller
{
    public function list(){
        $sessionData = session()->all();
        //dd($sessionData);
        $appraiser_officer_heads_id = $sessionData['logged_user_heads_id'];
        $current_appraisal_cycle = $sessionData['current_appraisal_cycle'];
        $ratings = DB::table('appraisal_form')
                    ->join('employee_goal_ratings', 'appraisal_form.employee_heads_id', '=', 'employee_goal_ratings.employee_heads_id')
                    ->leftJoin('internal_users', 'employee_goal_ratings.employee_heads_id', '=', 'internal_users.heads_id')
                    ->select(
                        'appraisal_form.id','appraisal_form.appraisal_category',
                        'employee_goal_ratings.employee_heads_id',
                        'internal_users.emp_code',
                        'internal_users.designation_name',
                        DB::raw("CONCAT(internal_users.first_name, ' ', internal_users.last_name) as full_name"),
                        DB::raw("CASE WHEN appraisal_form.self_finalise = 1 THEN 'Finalised' ELSE 'Not Finalised' END as finalise_status")
                    )
                    ->where('appraisal_form.appraiser_officer_heads_id', $appraiser_officer_heads_id)
                    ->where('appraisal_form.appraisal_cycle_id', $current_appraisal_cycle)
                    ->where('appraisal_form.status', 1)
                    ->groupBy(
                        'appraisal_form.id',
                        'appraisal_form.self_finalise',
                        'employee_goal_ratings.employee_heads_id',
                        'internal_users.emp_code',
                        'internal_users.designation_name',
                        'internal_users.first_name',
                        'internal_users.last_name'
                    )
                    ->get();

        //dd($ratings);
        return view('listusers', compact('ratings'));
    }
}
