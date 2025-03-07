<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AppraisalFormService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\AppraisalCycle;
use Illuminate\Support\Facades\DB;

class AppraisalFormController extends Controller
{
    protected $appraisalFormService;

    public function __construct(AppraisalFormService $appraisalFormService)
    {
        $this->appraisalFormService = $appraisalFormService;
    }
    public function index(Request $request){
        $sessionData = session()->all();
        $appraiserOfficerName = $sessionData['appraiserOfficerName'];
        $appraisalData = AppraisalCycle::select('appraisal_period')->where('status',1)->first();
        $user = Auth::user();
        $userHeadsId = $sessionData['logged_user_heads_id'];
        $appraisalCycle = $sessionData['current_appraisal_cycle'];
        $appraisal_form_id = $sessionData['appraisal_form_id'];
        /**
        * Grant access to the employee for the appraisal form. 
        * Here, we will check if the logged-in user is added to the current appraisal cycle.
        */
        if($appraisal_form_id == 0)
        {
            return view('user_not_in_appraisal');
        }

        /**
        * Employee details Array 
        * This block of code contains the self appraisal logged in user details
        */
            $employeeData = [
                'profile_pic' => $user->profile_pic,
                'name'  => $user->first_name . ' ' . $user->last_name,
                'emp_code' => $user->emp_code,
                'designation_name' => $user->designation_name,
                'date_of_join' => $user->date_of_join,
                'appraisal_period' => $appraisalData->profile_pic,
                'appraiserOfficerName' => $appraiserOfficerName
            ];
        /**
        * 
        * Code Ends Here
        *
        */

        /**
        * Employee Goal details Array 
        * This block of code contains the goal and project details of the logged in user for the current appraisal cycle
        */
            $user_goals =  DB::table('goals')
                            ->select('id','goal','employee_heads_id','appraisal_cycle','weightage')
                            ->where('appraisal_cycle', $appraisalCycle)
                            ->where('employee_heads_id', $userHeadsId)
                            ->get();

            $user_projects = DB::table('project_allocations')
                            ->select('projects.parats_project_id','projects.project_name')
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
        /**
        * 
        * Code Ends Here
        *
        */


        //return view('my_appraisal', compact('user','appraisalData','appraiserOfficerName','user_goals', 'user_projects', 'goalWiseData'));
        return view('my_appraisal', [
            'employeeData' => $employeeData,
            'user_goals' => $user_goals,
            'user_projects' => $user_projects,
            'goalWiseData' => $goalWiseData,
        ]);
    }


    public function submitEmpGoals(Request $request)
    {
        $sessionData = session()->all();
        $appraiserOfficerName = $sessionData['appraiserOfficerName'];
        $userHeadsId = $sessionData['logged_user_heads_id'];
        $appraisalCycle = $sessionData['current_appraisal_cycle'];

        $submittedGoalRatings = DB::table('employee_goal_ratings')
                                ->where('appraisal_cycle', $appraisalCycle)
                                ->where('employee_heads_id', $userHeadsId)
                                ->exists(); 
        //echo '<pre>'; print_r($submittedGoalRatings); die();
        if ($submittedGoalRatings) {
            // Delete existing records
            DB::table('employee_goal_ratings')
                ->where('appraisal_cycle', $appraisalCycle)
                ->where('employee_heads_id', $userHeadsId)
                ->delete();
        }
        $user_goals =  DB::table('goals')
                       ->select(
                            'id','goal','employee_heads_id','appraisal_cycle','weightage'
                        )
                        ->where('appraisal_cycle', $appraisalCycle)
                        ->where('employee_heads_id', $userHeadsId)
                        ->get();
        //echo '<pre>'; print_r($request->input()); die();           
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

        return redirect()->route('myappraisal');

    }

    public function reviewEmployeeList()
    {
        $employees = $this->appraisalFormService->getCurrentCycleEmployee();
        return view('empolyee_review_listing', compact('employees'));
    }

    
}
