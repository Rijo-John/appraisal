<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
class GoalController extends Controller
{
    public function index()
    {
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

        return view('goals_listing_page', compact('user_goals', 'user_projects', 'goalWiseData'));
    }
    
    public function submitEmpGoals(Request $request)
    {
        $userHeadsId = 2305;
        $appraisalCycle = 1;
        $submittedGoalRatings = DB::table('employee_goal_ratings')
                                ->where('appraisal_cycle', $appraisalCycle)
                                ->where('employee_heads_id', $userHeadsId)
                                ->exists(); 
        if ($submittedGoalRatings) {
            // Delete existing records
            DB::table('employee_goal_ratings')
                ->where('appraisal_cycle', $appraisalCycle)
                ->where('employee_heads_id', $userHeadsId)
                ->delete();
        }
        
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
        foreach ($user_goals as $goals) {
            $projectCount = $request->input('hiddenCount'.$goals->id);
            for($i = 1;$i<=$projectCount;$i++)
            {
                $projectId = 'project_' . $goals->id . '_' . $i;
                $ratingValue = 'rating_' . $goals->id . '_' . $i;
                $empremarks = 'remarks_' . $goals->id . '_' . $i;
                
                if ($request->has($projectId) && $request->filled($projectId) && 
                    $request->has($ratingValue) && $request->filled($ratingValue)) {
                    DB::table('employee_goal_ratings')->insert([
                        'appraisal_cycle' => $appraisalCycle,
                        'employee_heads_id' => $userHeadsId,
                        'goal_id' => $goals->id,
                        'parats_project_id' => $request->input($projectId),
                        'rating' => $request->input($ratingValue),
                        'employee_comment' => $request->input($empremarks)
                    ]);
                }
            }
        }

       // return view('goals_listing_page', compact('user_goals', 'user_projects'));
    }
}
