<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use App\Models\AppraisalCycle;

class AppraiserEvaluationController extends Controller
{
    public function index(Request $request){
        $appraisal_form_id = 2;
        $appraisalDetails =  DB::table('appraisal_form')
                            ->select('*')
                            ->where('id', $appraisal_form_id)
                            ->get();
        $appraisal_category = $appraisalDetails[0]->appraisal_category;  
        $appraiserOfficerName = $appraisalDetails[0]->appraiser_officer_name;
        $userHeadsId = $appraisalDetails[0]->employee_heads_id;
        $appraisalCycle = $appraisalDetails[0]->appraisal_cycle_id;
        $appraisalData = AppraisalCycle::select('appraisal_period')->where('status',1)->first();
        $employeeratingdata = DB::table('employee_goal_ratings as rating')
                            ->leftJoin('goals', 'rating.goal_id', '=', 'goals.id')
                            ->leftJoin('projects', 'rating.parats_project_id', '=', 'projects.parats_project_id')
                            ->leftJoin('internal_users', 'rating.employee_heads_id', '=', 'internal_users.heads_id')
                            ->where('rating.appraisal_form_id', $appraisal_form_id)
                            ->select('projects.project_name','internal_users.heads_id','internal_users.first_name','internal_users.last_name','internal_users.profile_pic','internal_users.emp_code'
                            ,'internal_users.designation_name','internal_users.date_of_join', 'goals.goal', 'rating.*')
                            ->orderBy('rating.id')
                            ->get();

        $employeeRatingProjectWise = [];
        foreach ($employeeratingdata as $item) {
            $project = $item->parats_project_id;
            if (!isset($employeeRatingProjectWise[$project])) {
                $employeeRatingProjectWise[$project] = [];
            }
            $employeeRatingProjectWise[$project][] = $item;
        }

        $employeeratingdata = DB::table('employee_goal_ratings as rating')
                            ->leftJoin('goals', 'rating.goal_id', '=', 'goals.id')
                            ->leftJoin('projects', 'rating.parats_project_id', '=', 'projects.parats_project_id')
                            ->leftJoin('internal_users', 'rating.employee_heads_id', '=', 'internal_users.heads_id')
                            ->where('rating.appraisal_form_id', $appraisal_form_id)
                            ->select('projects.project_name','internal_users.heads_id','internal_users.first_name','internal_users.last_name','internal_users.profile_pic','internal_users.emp_code'
                            ,'internal_users.designation_name','internal_users.date_of_join', 'goals.goal', 'rating.*')
                            ->orderBy('rating.id')
                            ->get();
        /**
        * Employee details Array 
        * This block of code contains the self appraisal logged in user details
        */
            $employeeData = [
                'appraisee_heads_id' => $employeeratingdata[0]->heads_id,
                'profile_pic' => $employeeratingdata[0]->profile_pic,
                'name'  => $employeeratingdata[0]->first_name . ' ' . $employeeratingdata[0]->last_name,
                'emp_code' => $employeeratingdata[0]->emp_code,
                'designation_name' => $employeeratingdata[0]->designation_name,
                'date_of_join' => $employeeratingdata[0]->date_of_join,
                'appraisal_period' => $appraisalData->appraisal_period,
                'appraiserOfficerName' => $appraiserOfficerName,
            ];
        /**
        * Code Ends Here
        */
       // $grouped = $employeeratingdata->groupBy('parats_project_id');
        
        
        //echo '<pre>'; print_r($employeeData); die();
        return view('appraiser_evaluation', [
            'employeeData' => $employeeData,
            'employeeratingdata' => $employeeRatingProjectWise,
            'project_extra' => [],
            'general_data' => [],
            'vigyanCourseDetails' => [],
            'certificationsfromHeads' => [],
            'selfFinalise' => [],
        ]);
    }


    public function appraiserSubmitEmpRating(Request $request)
    {
        if ($request->filled('employee_goal_rating_ids')) {
            $employeeGoalRatingIds = $request->input('employee_goal_rating_ids');
            $appraisee_heads_id = $request->input('appraisee_heads_id');
            foreach($employeeGoalRatingIds as $ratingId) {
                $dataToUpdate = [];
                $ratingValue = 'appraiser_rating_'  . $ratingId;
                $appraiserremarks = 'appraiser_remarks_' . $ratingId;
                $appraiser_rating = $request->input($ratingValue);
                $appraiser_comment = $request->input($appraiserremarks);
                if($appraiser_rating != ''){
                    $dataToUpdate = [
                        'appraiser_rating'     => $appraiser_rating,
                        'appraiser_comment'   => $appraiser_comment,
                        'appraiser_attachment'   => ''
                    ];
                    
                    DB::table('employee_goal_ratings')
                        ->where('id', $ratingId)
                        ->where('employee_heads_id', $appraisee_heads_id)
                        ->update($dataToUpdate);
                }
                
            }
           
        } else {
            // Handle the error: employee_goal_rating_ids field is not set or is empty.
        }
    }
   
}
