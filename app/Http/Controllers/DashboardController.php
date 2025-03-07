<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\AppraisalCycle;
use Illuminate\Support\Facades\DB;
class DashboardController extends Controller
{
   
    public function index(Request $request){
        $sessionData = session()->all();
        $appraiserOfficerName = $sessionData['appraiserOfficerName'];
        $appraisalData = AppraisalCycle::select('appraisal_period')->where('status',1)->first();
        $user = Auth::user();
        //dd($user);

        $user->name = $user->first_name . ' ' . $user->last_name;

        $userHeadsId = 2305;
        $appraisalCycle = 1;
        $user_goals =  DB::table('goals')
                        ->select(
                            'id',
                            'goal',
                            'employee_heads_id',
                            'appraisal_cycle',
                            'weightage'
                        )
                        ->where('appraisal_cycle', $appraisalCycle)
                        ->where('employee_heads_id', $userHeadsId)
                        ->get();
        $user_projects =  DB::table('project_allocations')
                       ->select(
                            'projects.parats_project_id','projects.project_name'
                        )
                        ->leftJoin('projects', 'projects.parats_project_id', '=', 'project_allocations.parats_project_id')
                        ->where('project_allocations.heads_id', $userHeadsId)
                        ->get();
        $submitted_goal_ratings =  DB::table('employee_goal_ratings')
                                   ->select('*')
                                    ->where('appraisal_cycle', $appraisalCycle)
                                    ->where('employee_heads_id', $userHeadsId)
                                    ->get();
        $goalWiseData = [];

        foreach ($submitted_goal_ratings as $item) {
            $goalId = $item->goal_id;
            if (!isset($goalWiseData[$goalId])) {
                $goalWiseData[$goalId] = [];
            }
            $goalWiseData[$goalId][] = $item;
        }

        //return view('goals_listing_page', compact('user_goals', 'user_projects', 'goalWiseData'));



        return view('dashboard', compact('user','appraisalData','appraiserOfficerName','user_goals', 'user_projects', 'goalWiseData'));
    }
}
