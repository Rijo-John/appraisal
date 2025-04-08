<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AppraisalFormService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\AppraisalCycle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppraisalFinalizedNotification;
use Barryvdh\DomPDF\Facade\Pdf;


class AppraisalFormController extends Controller
{
    protected $appraisalFormService;

    public function __construct(AppraisalFormService $appraisalFormService)
    {
        $this->appraisalFormService = $appraisalFormService;
    }
    
    public function index(Request $request){
        /**
        * Common Session Data
        * This block of code contains scommon data used for both technical and non-technical functions.
        */
            $sessionData = session()->all();
            $appraisal_category = $sessionData['appraisal_category'];  
            $appraiserOfficerName = $sessionData['appraiserOfficerName'];
            $appraisalData = AppraisalCycle::select('appraisal_period')->where('status',1)->first();
            $user = Auth::user();
            $userHeadsId = $sessionData['logged_user_heads_id'];
            $appraisalCycle = $sessionData['current_appraisal_cycle'];
            $appraisal_form_id = $sessionData['appraisal_form_id'];
            $appraisalEndDate = $appraisalStartDateYMD = $appraisalEndDateYMD = Carbon::now()->format('Y-m-d');
            
        /**
        * Code Ends Here
        */
            if($appraisal_form_id == 0)
            {
                return view('user_not_in_appraisal');
            }

            $selfFinalise = DB::table('appraisal_form')
                            ->where('employee_heads_id', $userHeadsId)
                            ->where('id', $appraisal_form_id)
                            ->where('appraisal_cycle_id', $appraisalCycle)
                            ->value('self_finalise');
            $appraisalCycleData =  DB::table('appraisal_cycle')
                            ->select('appraisal_cycle','appraisal_period_start','appraisal_period_end')
                            ->where('id', $appraisalCycle)
                            ->where('status', 1)
                            ->get();
            if($appraisalCycleData) {
                if (!empty($appraisalCycleData) && !empty($appraisalCycleData[0]->appraisal_period_start) &&  !empty($appraisalCycleData[0]->appraisal_period_end)) {
                    $appraisalStartDateYMD = $appraisalCycleData[0]->appraisal_period_start;
                    $appraisalEndDateYMD = $appraisalCycleData[0]->appraisal_period_end;
                }
            }

        ////////////////////////////////// VIGYAN TRAINING AND CERTIFICATION DATA //////////////////////////////////////////
        ////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        /**
        * Get learning details from Vigyan 
        * This block of code contains the vigyan learning hours and course details of the logged-in user
        */
            $vigyanCourseDetails = [];
            if($appraisalCycleData) {
                if (!empty($appraisalCycleData) && !empty($appraisalCycleData[0]->appraisal_period_start) &&  !empty($appraisalCycleData[0]->appraisal_period_end) && $user->email!='') {
                    $appraisalEndDate = $appraisalCycleData[0]->appraisal_period_end;
                    $baseUrl = env('VIGYAN_API_URL');
                    $params = [
                        'wstoken' => env('VIGYAN_API_TOKEN'),
                        'wsfunction' => env('VIGYAN_API_FUNCTION'),
                        'moodlewsrestformat' => 'json',
                        'start_date' => $appraisalCycleData[0]->appraisal_period_start,
                        'end_date' => $appraisalCycleData[0]->appraisal_period_end,
                        'email' => $user->email
                    ];
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json'
                    ])->get($baseUrl, $params);
                    $vigyanData = $response->json(); 
                    //echo '<pre>'; print_r($vigyanData); die();
                    $total_dedication = 0;
                    $totalVigyanTimeSpentSec = 0;
                    $targeted_hours_sec = 0;
                    $vigyanCourseDetails['courses'] = [];
                    $vigyanCourseDetails['timeSpent'] = 0;
                    $vigyanCourseDetails['calculatePercentage'] = 0;
                    $vigyanCourseDetails['training_name'] = [];
                    $vigyanCourseDetails['trainingTimeSpent'] = 0;
                    $vigyanCourseDetails['totalVigyanTimeSpent'] = 0;
                    $totaltrainingDuration = 0; 
                    $vigyanTime = 0;
                    $trainingTime = 0;
                    //echo '<pre>'; print_r($vigyanData); die();
                    if(isset($vigyanData['vigyan_training_details']) && count($vigyanData['vigyan_training_details']) > 0 )
                    {
                        
                        $vigyanCourseDetails['courses'] = [];
                        $vigyanCourseDetails['timeSpent'] = 0;
                        $vigyanCourseDetails['calculatePercentage'] = "NA";
                        $vigyanCourseDetails['training_name'] = [];
                        $vigyanCourseDetails['trainingTimeSpent'] = 0;
                        $vigyanCourseDetails['totalVigyanTimeSpent'] = 0;
                        if(count($vigyanData['vigyan_training_details']) > 0) {
                            $totalDuration = 0; 
                            foreach($vigyanData['vigyan_training_details'] as $index => $vigyan)
                            {
                                $vigyanCourseDetails['courses'][] = $vigyan['course_name'];
                                $totalDuration += $vigyan['time_spent']; 
                            }
                            $hours = floor($totalDuration / 3600); 
                            $minutes = floor(($totalDuration % 3600) / 60); 
                            $vigyanCourseDetails['timeSpent'] = $hours." Hours ".$minutes ." Minutes";
                            $vigyanTime = $totalDuration;
                            $total_dedication = $vigyanData['total_dedication'];
                            $targeted_hours_sec  = $vigyanData['targeted_hours'] * 60 * 60;
                            
                        } 
                        
                    }
                    if(isset($vigyanData['internal_hours']) && count($vigyanData['internal_hours']) > 0) {
                        
                        foreach($vigyanData['internal_hours'] as $index => $training)
                        {
                            $vigyanCourseDetails['training_name'][] = $training['training_name'];
                            $totaltrainingDuration += $training['time_spent']; 
                        }
                        $vigyanCourseDetails['trainingTimeSpent'] = $totaltrainingDuration." Hours";
                        $trainingTime = $totaltrainingDuration;
                        
                    }
                    if($total_dedication+$totalVigyanTimeSpentSec > 0 && $targeted_hours_sec >0)
                    {
                        $calculatePercentage = (($total_dedication+$totalVigyanTimeSpentSec)/$targeted_hours_sec) * 100;
                        $vigyanCourseDetails['calculatePercentage'] = round($calculatePercentage,2);
                    } 
                    $totalVigyanTimeSpentSec = ($totaltrainingDuration*60*60)+$vigyanTime;
                    $hours = floor($totalVigyanTimeSpentSec / 3600); 
                    $minutes = floor(($totalVigyanTimeSpentSec % 3600) / 60); 
                    $vigyanCourseDetails['totalVigyanTimeSpent'] = $hours." Hours ".$minutes ." Minutes";  
                
                } else {
                    echo 'No valid appraisal period start date found.';
                    exit();
                }
            }
            

        /**
        * Code Ends Here
        */

        /**
        * Get Qualifications and Certifications from HEADS 
        * This block of code contains the details of Qualifications and Certifications from HEADS 
        */

            $currentMonth = Carbon::now()->month;
            $appraisalMonth = ($currentMonth < 7) ? 1 : 2;
            $formattedAppraisalEndDate = Carbon::parse($appraisalEndDate)->format('Y-m');
            $params = [
                'url' => env('HEADS_CERTIFICATION_URL'),
                'appraisalMonthType' => (int) $appraisalMonth,
                'employeeId' =>  (int) $user->heads_id, // 11
                'appraisalMonth' => $formattedAppraisalEndDate //"2025-04",
            ];
            $certificationsfromHeads = $this->apiCallToGetCertificationDetailsHeads($params);
            //echo '<pre>'; print_r($certificationsfromHeads); die();
            if (isset($certificationsfromHeads->AppraisalCertListDataResponse) && count($certificationsfromHeads->AppraisalCertListDataResponse) > 0) {
                // Your logic here
            }
            else
            {
                $certificationsfromHeads = new \stdClass();
                $certificationsfromHeads->AppraisalCertListDataResponse = [];
            }
            
        /**
        * Code Ends Here
        */
        
        /////////////////////////////////////////////////////////////////////////////////////////////////////////////////////////
        
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
                'appraiserOfficerName' => $appraiserOfficerName,
            ];
        /**
        * Code Ends Here
        */
        //dd($appraisal_category);
        if($appraisal_category == 2)  // by sooraj
        {
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
            $submittedGeneralData = DB::table('general_data_by_appraisee')
                        ->where('appraisal_cycle', $appraisalCycle)
                        ->where('employee_heads_id', $userHeadsId)
                        ->where('appraisal_form_id', $appraisal_form_id)
                        ->first() ?? (object) ['key_contributions' => '', 'suggestions_for_improvement' => ''];
            //dd($submittedGeneralData);
            $goalWiseData = [];
            foreach ($submitted_goal_ratings as $item) {
                $goalId = $item->goal_id;
                if (!isset($goalWiseData[$goalId])) {
                    $goalWiseData[$goalId] = [];
                }
                $goalWiseData[$goalId][] = $item;
            }
           
            return view('my_appraisal', [
                'employeeData' => $employeeData,
                'user_goals' => $user_goals,
                'user_projects' => $user_projects,
                'goalWiseData' => $goalWiseData,
                'selfFinalise' => $selfFinalise,
                'submittedGeneralData'=>$submittedGeneralData,
                'vigyanCourseDetails' => $vigyanCourseDetails,
                'certificationsfromHeads' => $certificationsfromHeads,
            ]);
        }
        else if($appraisal_category == 1)          ////////////////////////////////////////////////// By Rijo ////////////////////////
        {
            /**
            * Employee Goal details Array 
            * This block of code contains the goal and project details of the logged in user for the current appraisal cycle
            */
                $user_goals =  DB::table('goals')
                                ->select('id','goal','employee_heads_id','appraisal_cycle','weightage')
                                ->where('appraisal_cycle', $appraisalCycle)
                                ->where('employee_heads_id', $userHeadsId)
                                ->get();

                $user_projects =  $this->getEmployeeProjects($userHeadsId);
                $submitted_goal_ratings =  $this->getEmployeeGoalRatingdata($appraisalCycle,$userHeadsId,$appraisal_form_id);
                $submitted_project_extra =  $this->getEmployeeProjectExtra($appraisalCycle,$userHeadsId,$appraisal_form_id);
                $submitted_general_data =  $this->getEmployeeGeneralData($appraisalCycle,$userHeadsId,$appraisal_form_id);
                                
                $projectWiseData = [];
                foreach ($submitted_goal_ratings as $item) {
                    $projectId = $item->parats_project_id;
                    if (!isset($projectWiseData[$projectId][$item->goal_id])) {
                        $projectWiseData[$projectId][$item->goal_id] = [];
                    }
                    $projectWiseData[$projectId][$item->goal_id][] = $item;
                }
                
                $projectExtraData = [];
                foreach ($submitted_project_extra as $item) {
                    $projectId = $item->parats_project_id;
                    if (!isset($projectExtraData[$projectId])) {
                        $projectExtraData[$projectId] = [];
                    }
                    $projectExtraData[$projectId][] = $item;
                }
               
                
            /**
            * Code Ends Here
            */
            //echo '<pre>'; print_r($projectWiseData); die();
        
            return view('my_appraisal', [
                'employeeData' => $employeeData,
                'user_goals' => $user_goals,
                'user_projects' => $user_projects,
                'projectWiseData' => $projectWiseData,
                'project_extra' => $projectExtraData,
                'general_data' => $submitted_general_data,
                'vigyanCourseDetails' => $vigyanCourseDetails,
                'certificationsfromHeads' => $certificationsfromHeads,
                'selfFinalise' => $selfFinalise,
            ]);
        } else {
            return redirect()->route('nopermission');
        }
    }
    public function submitEmpGoals(Request $request)
    {
        $sessionData = session()->all();
        $appraiserOfficerName = $sessionData['appraiserOfficerName'];
        $appraisal_category = $sessionData['appraisal_category'];  
        $userHeadsId = $sessionData['logged_user_heads_id'];
        $appraisalCycle = $sessionData['current_appraisal_cycle'];
        $appraisal_form_id = $sessionData['appraisal_form_id'];
        $appraisalEndDate = $appraisalStartDateYMD = $appraisalEndDateYMD = Carbon::now()->format('Y-m-d');
        
        $appraiseeSubCategory =  DB::table('appraisal_form')
                                ->select('appraisal_sub_category','self_finalise')
                                ->where('id', $appraisal_form_id)
                                ->where('employee_heads_id', $userHeadsId)
                                ->where('status', 1)
                                ->get();
        $self_finalise = 1;
        if(count($appraiseeSubCategory) > 0) {
            $self_finalise = $appraiseeSubCategory[0]->self_finalise;
        }
        /**
        * Grant access to the employee for the appraisal form. 
        * Here, we will check if the logged-in user is added to the current appraisal cycle.
        */
            if($appraisal_form_id == 0) {
                return redirect()->route('myappraisal')->with('error-user-not-in-appraisal', 'You are not part of the current appraisal cycle.');
            }
        /**
        * Code Ends Here
        */  
        if($appraisal_category == 1 && $self_finalise == 0)        /////////////////   Code by Rijo SUBMIT ////////////////////////////////
        {
            $user_goals =  DB::table('goals')
                            ->select(
                                    'id','goal','employee_heads_id','appraisal_cycle','weightage'
                            )
                            ->where('appraisal_cycle', $appraisalCycle)
                            ->where('employee_heads_id', $userHeadsId)
                            ->get();
            $user_projects =  $this->getEmployeeProjects($userHeadsId);
            if(count($user_goals) > 0 && count($user_projects) > 0) { 
                $validationRules = [];
                $customMessages = [];
                if ($request->input('is_finalise') == '1') {
                    foreach ($user_projects as $projects)
                    {
                        $i = 1;
                        foreach($user_goals as $goals)
                        {
                            $ratingValue = 'rating_' . $projects->parats_project_id . '_' . $goals->id;
                            $validationRules[$ratingValue] = 'required|in:1,2,3,4,0';

                            // Custom error message with index
                            $customMessages["$ratingValue.required"] = "All Rating is required for Project #".$projects->project_name. " - Goal #".$i;
                            $customMessages["$ratingValue.in"] = "Invalid rating selected for Project #".$projects->project_name. " - Goal #".$i;
                            $i++;
                        }
                    }
                    $validator = Validator::make($request->all(), $validationRules, $customMessages);
                    if ($validator->fails()) {
                        return redirect()->back()->withErrors($validator)->withInput();
                    }
                }
                
                /**
                * Delete goals rating and key contributions from table.
                * Here, we will check whether goals rating and key contributions are already inserted if yes delete in case of updation. 
                */

                    $this->deleteExistingUserSelfRatingData($appraisalCycle,$userHeadsId,$appraisal_form_id);

                /**
                * Code Ends Here
                */  

                /**
                * Insert project - goals wise rating 
                * This block of code insert  the goal and rating of each project
                */
                
                
                    foreach ($user_projects as $projects)
                    {
                        $taskdetails = 'taskdetails' . $projects->parats_project_id;
                        DB::table('project_extra')->insert([
                            'appraisal_cycle' => $appraisalCycle,
                            'appraisal_form_id' => $appraisal_form_id,
                            'employee_heads_id' => $userHeadsId,
                            'parats_project_id' => $projects->parats_project_id,
                            'task_details' => $request->input($taskdetails)
                        ]);
                    
                        
                        foreach($user_goals as $goals)
                        {
                            $goalRatingData = [];
                            $ratingValue = 'rating_' . $projects->parats_project_id . '_' . $goals->id;
                            $empremarks = 'remarks_' . $projects->parats_project_id . '_' . $goals->id;

                            $fileInputName = 'evidence_' . $projects->parats_project_id . '_' . $goals->id;
                            $attachmentPath = null;
                
                            if ($request->hasFile($fileInputName)) {
                                $file = $request->file($fileInputName);
                                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                                $cleanFilename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalFilename);
                                $extension = $file->getClientOriginalExtension();
                                $timestamp = time();
                                $newFilename = $cleanFilename.$goals->id.$timestamp.'.'.$extension;
                                $attachmentPath = $file->storeAs('uploads/evidence', $newFilename, 'public');
                            }else {
                                $oldAttachment =  $request->input('attachment_' . $projects->parats_project_id . '_' . $goals->id);
                                if($oldAttachment!='')
                                {
                                    $attachmentPath = $oldAttachment;
                                }
                            }
                            

                            $goalRatingData[] = [
                                'appraisal_cycle' => $appraisalCycle,
                                'appraisal_form_id'  => $appraisal_form_id,
                                'employee_heads_id' => $userHeadsId,
                                'goal_id' => $goals->id,
                                'parats_project_id' => $projects->parats_project_id,
                                'rating' => $request->input($ratingValue),
                                'employee_comment' => $request->input($empremarks),
                                'attachment' => $attachmentPath
                            ];
                            $this->appraisalFormService->insertToGoalRatings($goalRatingData);
                        }
                        
                    }

                /**
                * Code Ends Here
                */


                /**
                * Insert general goals rating 
                * This block of code insert  the goal and rating without any project
                */
                
                    foreach($user_goals as $goals)
                    {
                        $goalRatingDataOnce = [];
                        $ratingValue = 'general_rating_'  . $goals->id;
                        $empremarks = 'general_remarks_' . $goals->id;
                        $goalRatingDataOnce[] = [
                            'appraisal_cycle' => $appraisalCycle,
                            'appraisal_form_id'  => $appraisal_form_id,
                            'employee_heads_id' => $userHeadsId,
                            'goal_id' => $goals->id,
                            'parats_project_id' => -1,
                            'rating' => $request->input($ratingValue),
                            'employee_comment' => $request->input($empremarks)
                        ];
                        $this->appraisalFormService->insertToGoalRatings($goalRatingDataOnce);
                    }
                    DB::table('project_extra')->insert([
                            'appraisal_cycle' => $appraisalCycle,
                            'appraisal_form_id' => $appraisal_form_id,
                            'employee_heads_id' => $userHeadsId,
                            'parats_project_id' => -1,
                            'task_details' => $request->input('general_taskdetails')
                    ]);
                /**
                * Code Ends Here
                */

                /**
                * Insert Suggestions and key contributions 
                * This block of code insert the Suggestions and key contributions for Organization’s Improvement by the appraisee
                */
                    DB::table('general_data_by_appraisee')->insert([
                        'appraisal_cycle' => $appraisalCycle,
                        'appraisal_form_id'  => $appraisal_form_id,
                        'employee_heads_id' => $userHeadsId,
                        'key_contributions' => $request->input('key_contributions'),
                        'suggestions_for_improvement' => $request->input('suggestions_for_improvement'),
                        'workshops_attended' => $request->input('employee_workshops'),
                        'trainings_conducted' => $request->input('employee_training_conducted')
                    ]);
                
                /**
                * Code Ends Here
                */
                if ($request->input('is_finalise') == '1') {
                    DB::table('appraisal_form')
                        ->where('employee_heads_id', $userHeadsId)
                        ->where('id', $appraisal_form_id)
                        ->where('appraisal_cycle_id', $appraisalCycle)
                        ->update(['self_finalise' => 1]);
                }
            }
        }
        else if($appraisal_category == 2) /////////////////////////   Code by Sooraj /////////////////////////////////////
        {
            //dd($request->all());
            $appraiserOfficerName = $sessionData['appraiserOfficerName'];
            $userHeadsId = $sessionData['logged_user_heads_id'];
            $appraisalCycle = $sessionData['current_appraisal_cycle'];
            $appraisalFormId = session('appraisal_form_id');
    
            $isFinalised = DB::table('appraisal_form')
                    ->where('employee_heads_id', $userHeadsId)
                    ->where('id', $appraisalFormId)
                    ->where('appraisal_cycle_id', $appraisalCycle)
                    ->value('self_finalise');
            if ($isFinalised == 1) {
                return redirect()->back()->withErrors(['error' => 'You cannot submit goals as they have already been finalised.']);
            }
    
            $submittedGoalRatings = DB::table('employee_goal_ratings')
                ->where('appraisal_cycle', $appraisalCycle)
            ->where('employee_heads_id', $userHeadsId)
            ->exists();
    
    
            /*if ($submittedGoalRatings) {
                DB::table('employee_goal_ratings')
                    ->where('appraisal_cycle', $appraisalCycle)
                    ->where('employee_heads_id', $userHeadsId)
                    ->delete();
    
            }*/
    
            $submittedGeneralData = DB::table('general_data_by_appraisee')
                ->where('appraisal_cycle', $appraisalCycle)
                ->where('employee_heads_id', $userHeadsId)
                ->where('appraisal_form_id', $appraisalFormId)
                ->exists();
            if($submittedGeneralData){
                DB::table('general_data_by_appraisee')
                    ->where('appraisal_cycle', $appraisalCycle)
                    ->where('employee_heads_id', $userHeadsId)
                    ->where('appraisal_form_id', $appraisalFormId)
                    ->delete();
            }
    
            /*$user_goals = DB::table('goals')
                ->select('id', 'goal', 'employee_heads_id', 'appraisal_cycle', 'weightage')
                ->where('appraisal_cycle', $appraisalCycle)
                ->where('employee_heads_id', $userHeadsId)
                ->get();*/
            $user_goals = DB::table('goals')
                ->leftJoin('employee_goal_ratings', 'goals.id', '=', 'employee_goal_ratings.goal_id')
                ->select(
                    'goals.id',
                    'goals.goal',
                    'goals.employee_heads_id',
                    'goals.appraisal_cycle',
                    'goals.weightage',
                    'employee_goal_ratings.rating',
                    'employee_goal_ratings.employee_comment',
                    'employee_goal_ratings.attachment'
                )
                ->where('goals.appraisal_cycle', $appraisalCycle)
                ->where('goals.employee_heads_id', $userHeadsId)
                ->get();
                
            //dd($user_goals);

            
            $validationRules = [];
            $customMessages = [];
            $goalIndex = 1;
    
            // **Check if the user clicked "Finalise"**
            if ($request->input('is_finalise') === '1') {
                foreach ($user_goals as $goals) {
                    $ratingValue = 'rating_' . $goals->id;
                    $validationRules[$ratingValue] = 'required|in:0,1,5,10';

                    // Custom error message with index
                    $customMessages["$ratingValue.required"] = "Rating is required for Goal #$goalIndex.";
                    $customMessages["$ratingValue.in"] = "Invalid rating selected for Goal #$goalIndex.";

                    $goalIndex++; // Increment index
                }
            }
    
            foreach ($user_goals as $goals) {
                $fileInputName = 'evidence_' . $goals->id;
                $validationRules[$fileInputName] = 'nullable|file|mimes:pdf,jpg,png|max:2048';

                $customMessages["$fileInputName.mimes"] = "The evidence file for goal ID {$goals->id} must be a PDF, JPG, or PNG.";
                $customMessages["$fileInputName.max"] = "The evidence file for goal ID {$goals->id} must not be larger than 2MB.";
            }
    
           
    
            $validator = Validator::make($request->all(), $validationRules, $customMessages);
    
            if ($validator->fails()) {
                $errors = $validator->errors();
                
                // Collect missing rating indexes
                $missingRatings = [];
                $goalIndex = 1; // Reset index
                foreach ($user_goals as $goals) {
                    $ratingValue = 'rating_' . $goals->id;
                    if ($errors->has($ratingValue)) {
                        $missingRatings[] = "#$goalIndex"; // Collect index of missing goal
                    }
                    $goalIndex++;
                }

                if (!empty($missingRatings)) {
                    $missingMessage = "Please select ratings for the following goals: " . implode(', ', $missingRatings);
                    return redirect()->back()->withErrors(['error' => $missingMessage])->withInput();
                }

                return redirect()->back()->withErrors($errors)->withInput();
            }
    
            //$request->validate($validationRules, $customMessages);
            $insertData = [];

            foreach ($user_goals as $goals) {
                $ratingValue = 'rating_' . $goals->id;
                $empremarks = 'remarks_' . $goals->id;
                $fileInputName = 'evidence_' . $goals->id;
                //$attachmentPath = null;

                $existingAttachment = DB::table('employee_goal_ratings')
                    ->where('appraisal_cycle', $appraisalCycle)
                    ->where('employee_heads_id', $userHeadsId)
                    ->where('goal_id', $goals->id)
                    ->value('attachment');

                $attachmentPath = $existingAttachment;
    
                if ($request->hasFile($fileInputName)) {
                    $file = $request->file($fileInputName);
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);                    
                    $cleanFilename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalFilename);
                    $extension = $file->getClientOriginalExtension();
                    $timestamp = time();
                    $newFilename = $timestamp . '_' . $goals->id . '_' . $cleanFilename . '.' . $extension;
                    $attachmentPath = $file->storeAs('uploads/evidence', $newFilename, 'public');
                }
    
                
    
                DB::table('employee_goal_ratings')->updateOrInsert(
                    [
                        'appraisal_cycle' => $appraisalCycle,
                        'employee_heads_id' => $userHeadsId,
                        'goal_id' => $goals->id,
                        'appraisal_form_id' => $appraisalFormId
                    ],
                    [
                        'parats_project_id' => 0,
                        'rating' => $request->input($ratingValue),
                        'employee_comment' => $request->input($empremarks),
                        'attachment' => $attachmentPath
                    ]
                );
    
                
            }
    
            DB::table('general_data_by_appraisee')->insert([
                'appraisal_form_id' => $appraisalFormId,
                'appraisal_cycle' => $appraisalCycle,
                'employee_heads_id' => $userHeadsId,
                'key_contributions' => $request->input('key_contribution'),
                'suggestions_for_improvement' => $request->input('appraiser_comment')
            ]);
    
            if ($request->input('is_finalise') === '1') {
                $ratingLabels = [
                    10 => 'Achieved',
                    5  => 'Partially Achieved',
                    1  => 'Not Achieved',
                    0  => 'Not Applicable'
                ];
                //dd($user_goals);
                $submitted_general_data =  $this->getEmployeeGeneralData($appraisalCycle,$userHeadsId,$appraisalFormId);
                //dd($submitted_general_data);
                DB::table('appraisal_form')
                    ->where('employee_heads_id', $userHeadsId)
                    ->where('id', $appraisalFormId)
                    ->where('appraisal_cycle_id', $appraisalCycle)
                    ->update(['self_finalise' => 1]);

                $employeeDetails = DB::table('appraisal_form')
                    ->join('internal_users', 'appraisal_form.employee_heads_id', '=', 'internal_users.heads_id')
                    ->where('appraisal_form.employee_heads_id', $userHeadsId)
                    ->where('appraisal_form.id', $appraisalFormId)
                    ->where('appraisal_cycle_id', $appraisalCycle)
                    ->select(
                        'internal_users.email as employeeEmail',
                        DB::raw("CONCAT(internal_users.first_name, ' ', internal_users.last_name) as employeeName")
                    )
                    ->first();

                if ($employeeDetails && $employeeDetails->employeeEmail) {
                    $ccEmails = explode(',', env('APPRAISAL_START_MAIL_CC_ADDRESSES'));

                    // Prepare data to send to PDF view
                    $pdfData = [
                        'employeeDetails' => $employeeDetails,
                        'appraiserOfficerName' => $appraiserOfficerName,
                        'appraisalCycle' => $appraisalCycle,
                        'user_goals' => $user_goals,
                        'submittedGeneralData' => $submitted_general_data,
                        'ratingLabels' => $ratingLabels
                        // Add other data as needed
                    ];

                    $pdf = Pdf::loadView('appraisal.pdf', $pdfData);
                    $pdfContent = $pdf->output();

                    Mail::to($employeeDetails->employeeEmail)
                        ->cc($ccEmails)
                        ->send(new AppraisalFinalizedNotification(
                            $employeeDetails->employeeEmail, 
                            $employeeDetails->employeeName, 
                            $appraiserOfficerName, 
                            $appraisalCycle,
                            $pdfContent
                        ));
                }
            }
        }

        return redirect()->route('myappraisal');

    }


    private function apiCallToGetCertificationDetailsHeads(array $data)
    {
        $url = $data['url'];
        $appraisalMonth = $data['appraisalMonth'];
        $employeeId = $data['employeeId'];
        $appraisalMonthType = $data['appraisalMonthType'];
        $body = [
            'appraisalMonth' => $appraisalMonth,
            'employeeId' =>  $employeeId,
            'appraisalMonthType' => $appraisalMonthType
        ];
        $headers = [
            'X-Api-Key' => env('HEADS_X_API_KEY'),
            'Content-Type' => 'application/json-patch+json'
        ];
        $response = Http::withHeaders($headers)->post($url, $body);
        
        if ($response->failed()) {
            //dd("API Request Failed", $response->status(), $response->body());
            return '';
        }
        if ($response->successful()) {
            $data = $response->json();
            $decryptedResponse = json_decode($this->decryptAppraisalResponse($data['response']));
            return $decryptedResponse;
        }
      
    }
    public function decryptAppraisalResponse($encryptedData){
        $key = env('APPRAISALUSER_ENCRYPTION_KEY'); 
        $iv = env('APPRAISALUSER_IV'); 

        $decrypted = openssl_decrypt(
            base64_decode($encryptedData),
            'aes-256-cbc',
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        return $decrypted;
    }
    

    public function reviewEmployeeList()
    {
        $employees = $this->appraisalFormService->getCurrentCycleEmployee();
        return view('empolyee_review_listing', compact('employees'));
    }
    
    public function getEmployeeProjects($userHeadsId)
    {
        $user_projects_duplicate = DB::table('user_allocated_projects')
                            ->select('project_code','project_name', 'project_id as id', 'parats_project_id')
                            ->where('user_heads_id', $userHeadsId)
                            ->where('status', 1)
                            ->orderBy('project_name')
                            ->get();
        $user_projects = $user_projects_duplicate->unique('project_code')->map(function ($project) {
            $project->project_name = preg_replace('/\s*-ver\S*|\s*- Version\S*/i', '', $project->project_name);
            return $project;
        })->values();
        
        return count($user_projects) > 0 ? $user_projects : [];
    }

    public function getEmployeeGoalRatingdata($appraisalCycle,$userHeadsId,$appraisal_form_id)
    {
        $goal_ratings =  DB::table('employee_goal_ratings')
                        ->select('*')
                        ->where('appraisal_cycle', $appraisalCycle)
                        ->where('employee_heads_id', $userHeadsId)
                        ->where('appraisal_form_id', $appraisal_form_id)
                        ->get();
        return $goal_ratings;
    }
    
    
    public function getEmployeeProjectExtra($appraisalCycle,$userHeadsId,$appraisal_form_id)
    {
        $project_extra =  DB::table('project_extra')
                        ->select('*')
                        ->where('appraisal_cycle', $appraisalCycle)
                        ->where('employee_heads_id', $userHeadsId)
                        ->where('appraisal_form_id', $appraisal_form_id)
                        ->get();
        return $project_extra;
    }
    public function getEmployeeGeneralData($appraisalCycle,$userHeadsId,$appraisal_form_id)
    {
        $general_data =  DB::table('general_data_by_appraisee')
                        ->select('*')
                        ->where('appraisal_cycle', $appraisalCycle)
                        ->where('employee_heads_id', $userHeadsId)
                        ->where('appraisal_form_id', $appraisal_form_id)
                        ->first();
        return $general_data;
    }
    public function deleteExistingUserSelfRatingData($appraisalCycle,$userHeadsId,$appraisal_form_id)
    {
        $submittedGoalRatings = DB::table('employee_goal_ratings')
            ->where('appraisal_cycle', $appraisalCycle)
            ->where('employee_heads_id', $userHeadsId)
            ->where('appraisal_form_id', $appraisal_form_id)
            ->exists(); 
        $projectExtraDetails  = DB::table('project_extra')
            ->where('appraisal_cycle', $appraisalCycle)
            ->where('employee_heads_id', $userHeadsId)
            ->where('appraisal_form_id', $appraisal_form_id)
            ->exists(); 
        $generalData  = DB::table('general_data_by_appraisee')
            ->where('appraisal_cycle', $appraisalCycle)
            ->where('employee_heads_id', $userHeadsId)
            ->where('appraisal_form_id', $appraisal_form_id)
            ->exists(); 
        if ($submittedGoalRatings) {
            DB::table('employee_goal_ratings')
                ->where('appraisal_cycle', $appraisalCycle)
                ->where('employee_heads_id', $userHeadsId)
                ->where('appraisal_form_id', $appraisal_form_id)
                ->delete();
        }
        if ($projectExtraDetails) {
            DB::table('project_extra')
                ->where('appraisal_cycle', $appraisalCycle)
                ->where('employee_heads_id', $userHeadsId)
                ->where('appraisal_form_id', $appraisal_form_id)
                ->delete();
        }
        if ($generalData) {
            DB::table('general_data_by_appraisee')
                ->where('appraisal_cycle', $appraisalCycle)
                ->where('employee_heads_id', $userHeadsId)
                ->where('appraisal_form_id', $appraisal_form_id)
                ->delete();
        }

    }

    public function download($filename)
    {
        $filePath = storage_path('app/public/uploads/evidence/' . $filename);

        if (file_exists($filePath)) {
            return response()->download($filePath);
        } else {
            abort(404, 'File not found');
        }
    }

    public function delete(Request $request)
    {
        $request->validate([
            'goal_rating_id' => 'required',
            'goal_id' => 'required',
            'goal_rating_id' => 'required',
        ]);

        $attachmentPath = 'public/' . $request->attachment;

        if (Storage::exists($attachmentPath)) {
            Storage::delete($attachmentPath);
        }

        DB::table('employee_goal_ratings')
            ->where('id', $request->goal_rating_id)
            ->where('goal_id', $request->goal_id)
            ->update(['attachment' => null]);
        return response()->json(['success' => true]);
    }

    public function deleteAttachment(Request $request){
        //dd($request->all());
        $request->validate([
            'goal_rating_id' => 'required',
            'goal_id' => 'required',
        ]);

        $goalRatingId = $request->input('goal_rating_id');
        $goalId = $request->input('goal_id');

        $attachment = DB::table('employee_goal_ratings')
            ->where('id', $goalRatingId)
            ->where('goal_id', $goalId)
            ->value('attachment');
        //dd($attachment);

        if ($attachment) {
            // Correct file path for Storage facade
            if (Storage::disk('public')->exists($attachment)) {
                Storage::disk('public')->delete($attachment);
            }
        }

        DB::table('employee_goal_ratings')
            ->where('id', $goalRatingId)
            ->where('goal_id', $goalId)
            ->update(['attachment' => null]);
        return response()->json(['success' => true]);
    }


    public function refreshCertification(Request $request)
    {
        $sessionData = session()->all();
        $user = Auth::user();
        $appraisalEndDateYMD = '';
        $appraisalCycle = $sessionData['current_appraisal_cycle'];
        $appraisalCycleData =  DB::table('appraisal_cycle')
        ->select('appraisal_cycle','appraisal_period_start','appraisal_period_end')
        ->where('id', $appraisalCycle)
        ->where('status', 1)
        ->get();
        if($appraisalCycleData) {
            if (!empty($appraisalCycleData) && !empty($appraisalCycleData[0]->appraisal_period_start) &&  !empty($appraisalCycleData[0]->appraisal_period_end)) {
                $appraisalEndDateYMD = $appraisalCycleData[0]->appraisal_period_end;
            }
        }
        $currentMonth = Carbon::now()->month;
        $appraisalMonth = ($currentMonth < 7) ? 1 : 2;
        $formattedAppraisalEndDate = Carbon::parse($appraisalEndDateYMD)->format('Y-m');

        $params = [
            'url' => env('HEADS_CERTIFICATION_URL'),
            'appraisalMonthType' => (int) $appraisalMonth,
            'employeeId' =>  (int) $user->heads_id, // 11
            'appraisalMonth' => $formattedAppraisalEndDate //"2025-04",
        ];
        $certificationsfromHeads = $this->apiCallToGetCertificationDetailsHeads($params);
        if (isset($certificationsfromHeads->AppraisalCertListDataResponse) && count($certificationsfromHeads->AppraisalCertListDataResponse) > 0) {
            return response()->json([
                'status' => 'success',
                'message' => $certificationsfromHeads
            ], 200); 
        } else {
            return response()->json([
                'status' => 'error',
                'message' => 'No certifications found from head'
            ], 200); 
        }
        
        
    }


}
