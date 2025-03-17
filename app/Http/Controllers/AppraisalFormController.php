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
            // $vigyanCourseDetails = [];
            // if($appraisalCycleData) {
            //     if (!empty($appraisalCycleData) && !empty($appraisalCycleData[0]->appraisal_period_start) &&  !empty($appraisalCycleData[0]->appraisal_period_end) && $user->email!='') {
            //         $appraisalEndDate = $appraisalCycleData[0]->appraisal_period_end;
            //         $baseUrl = env('VIGYAN_API_URL');
            //         $params = [
            //             'wstoken' => env('VIGYAN_API_TOKEN'),
            //             'wsfunction' => env('VIGYAN_API_FUNCTION'),
            //             'moodlewsrestformat' => 'json',
            //             'start_date' => $appraisalCycleData[0]->appraisal_period_start,
            //             'end_date' => $appraisalCycleData[0]->appraisal_period_end,
            //             'email' => "aneesh.ks@thinkpalm.com", //$user->email
            //         ];
            //         $response = Http::withHeaders([
            //             'Content-Type' => 'application/json'
            //         ])->get($baseUrl, $params);
            //         $vigyanData = $response->json(); 

            //         $vigyanCourseDetails['courses'] = [];
            //         $vigyanCourseDetails['timeSpent'] = 0;
            //         $vigyanCourseDetails['calculatePercentage'] = 0;
            //         $vigyanCourseDetails['training_name'] = [];
            //         $vigyanCourseDetails['trainingTimeSpent'] = 0;
            //         $vigyanCourseDetails['totalVigyanTimeSpent'] = 0;

            //         if(count($vigyanData['vigyan_training_details']) > 0 || count($vigyanData['internal_hours']) > 0 )
            //         {
            //             $vigyanTime = 0;
            //             $trainingTime = 0;
            //             $vigyanCourseDetails['courses'] = [];
            //             $vigyanCourseDetails['timeSpent'] = 0;
            //             $vigyanCourseDetails['calculatePercentage'] = "NA";
            //             $vigyanCourseDetails['training_name'] = [];
            //             $vigyanCourseDetails['trainingTimeSpent'] = 0;
            //             $vigyanCourseDetails['totalVigyanTimeSpent'] = 0;
            //             if(count($vigyanData['vigyan_training_details']) > 0) {
            //                 $totalDuration = 0; 
            //                 foreach($vigyanData['vigyan_training_details'] as $index => $vigyan)
            //                 {
            //                     $vigyanCourseDetails['courses'][] = $vigyan['course_name'];
            //                     $totalDuration += $vigyan['time_spent']; 
            //                 }
            //                 $hours = floor($totalDuration / 3600); 
            //                 $minutes = floor(($totalDuration % 3600) / 60); 
            //                 $vigyanCourseDetails['timeSpent'] = $hours." Hours ".$minutes ." Minutes";
            //                 $vigyanTime = $totalDuration;
            //                 $total_dedication = $vigyanData['total_dedication'];
            //                 $targeted_hours_sec  = $vigyanData['targeted_hours'] * 60 * 60;
                            
            //             }
                        
            //             if(count($vigyanData['internal_hours']) > 0) {
            //                 $totaltrainingDuration = 0; 
            //                 foreach($vigyanData['internal_hours'] as $index => $training)
            //                 {
            //                     $vigyanCourseDetails['training_name'][] = $training['training_name'];
            //                     $totaltrainingDuration += $training['time_spent']; 
            //                 }
            //                 $vigyanCourseDetails['trainingTimeSpent'] = $totaltrainingDuration." Hours";
            //                 $trainingTime = $totaltrainingDuration;
            //                 $totalVigyanTimeSpentSec = ($totaltrainingDuration*60*60)+$vigyanTime;
            //                 $hours = floor($totalVigyanTimeSpentSec / 3600); 
            //                 $minutes = floor(($totalVigyanTimeSpentSec % 3600) / 60); 
            //                 $vigyanCourseDetails['totalVigyanTimeSpent'] = $hours." Hours ".$minutes ." Minutes";
            //             }

            //             if($total_dedication+$totalVigyanTimeSpentSec > 0 && $targeted_hours_sec >0)
            //             {
            //                 $calculatePercentage = (($total_dedication+$totalVigyanTimeSpentSec)/$targeted_hours_sec) * 100;
            //                 $vigyanCourseDetails['calculatePercentage'] = round($calculatePercentage,2);
            //             }
                        
            //         }
                
            //     } else {
            //         echo 'No valid appraisal period start date found.';
            //         exit();
            //     }
            // }
            

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
                'employeeId' => 11, // (int) $user->heads_id,
                'appraisalMonth' => $formattedAppraisalEndDate //"2025-04",
            ];
            $certificationsfromHeads = $this->apiCallToGetCertificationDetailsHeads($params);
            if (isset($certificationsfromHeads->AppraisalCertListDataResponse) && count($certificationsfromHeads->AppraisalCertListDataResponse) > 0) {
                // Your logic here
            }
            else
            {
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
                        ->first();
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
                //'vigyanCourseDetails' => $vigyanCourseDetails,
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

                // $user_projects = DB::table('project_allocations')
                //                 ->select('projects.parats_project_id','projects.project_name')
                //                 ->leftJoin('projects', 'projects.parats_project_id', '=', 'project_allocations.parats_project_id')
                //                 ->where('project_allocations.heads_id', $userHeadsId)
                //                 ->get();
                $user_projects =  $this->getEmployeeProjects($userHeadsId,$appraisalStartDateYMD,$appraisalEndDateYMD);
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

        
            return view('my_appraisal', [
                'employeeData' => $employeeData,
                'user_goals' => $user_goals,
                'user_projects' => $user_projects,
                'projectWiseData' => $projectWiseData,
                'project_extra' => $projectExtraData,
                'general_data' => $submitted_general_data,
               // 'vigyanCourseDetails' => $vigyanCourseDetails,
                'certificationsfromHeads' => $certificationsfromHeads,
                'selfFinalise' => $selfFinalise,
            ]);
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
        if($appraisal_category == 1)        /////////////////////////   Code by Rijo /////////////////////////////////////
        {
        //echo '<pre>'; print_r($request->input()); die();
            /**
            * Delete goals rating and key contributions from table.
            * Here, we will check whether goals rating and key contributions are already inserted if yes delete in case of updation. 
            */

                $this->deleteExistingUserSelfRatingData($appraisalCycle,$userHeadsId,$appraisal_form_id);

            /**
            * Code Ends Here
            */  

            $user_goals =  DB::table('goals')
                        ->select(
                                'id','goal','employee_heads_id','appraisal_cycle','weightage'
                            )
                            ->where('appraisal_cycle', $appraisalCycle)
                            ->where('employee_heads_id', $userHeadsId)
                            ->get();
            // $user_projects = DB::table('project_allocations')
            //                 ->select('projects.parats_project_id','projects.project_name')
            //                 ->leftJoin('projects', 'projects.parats_project_id', '=', 'project_allocations.parats_project_id')
            //                 ->where('project_allocations.heads_id', $userHeadsId)
            //                 ->get();
            $user_projects =  $this->getEmployeeProjects($userHeadsId,$appraisalStartDateYMD,$appraisalEndDateYMD);
        
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
                    $goalRatingData[] = [
                        'appraisal_cycle' => $appraisalCycle,
                        'appraisal_form_id'  => $appraisal_form_id,
                        'employee_heads_id' => $userHeadsId,
                        'goal_id' => $goals->id,
                        'parats_project_id' => $projects->parats_project_id,
                        'rating' => $request->input($ratingValue),
                        'employee_comment' => $request->input($empremarks)
                    ];
                    $this->appraisalFormService->insertToGoalRatings($goalRatingData);
                }
                
            }

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
                    echo '<pre>'; print_r($goalRatingDataOnce); 
                    //$this->appraisalFormService->insertToGoalRatings($goalRatingDataOnce);
                }
                die();
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
                $this->appraisalFormService->insertToGoalRatings($goalRatingDataOnce);
            
            /**
            * Code Ends Here
            */

        }
        else if($appraisal_category == 2) /////////////////////////   Code by Sooraj /////////////////////////////////////
        {
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
    
    
            if ($submittedGoalRatings) {
                DB::table('employee_goal_ratings')
                    ->where('appraisal_cycle', $appraisalCycle)
                    ->where('employee_heads_id', $userHeadsId)
                    ->delete();
    
            }
    
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
    
            $user_goals = DB::table('goals')
                ->select('id', 'goal', 'employee_heads_id', 'appraisal_cycle', 'weightage')
                ->where('appraisal_cycle', $appraisalCycle)
                ->where('employee_heads_id', $userHeadsId)
                ->get();
    
            
            $validationRules = [];
            $customMessages = [];
    
            // **Check if the user clicked "Finalise"**
            if ($request->input('action') === 'finalise') {
                foreach ($user_goals as $goals) {
                    $ratingValue = 'rating_' . $goals->id;
                    $validationRules[$ratingValue] = 'required|in:0,1,5,10'; 
    
                    $customMessages["$ratingValue.required"] = "Rating is required for  goals.";
                    $customMessages["$ratingValue.in"] = "Invalid rating selected for goal.";
                }
            }
    
            foreach ($user_goals as $goals) {
                $fileInputName = 'evidence_' . $goals->id;
                $validationRules[$fileInputName] = 'nullable|file|mimes:pdf,jpg,png|max:2048';
            }
    
            $customMessages = [
                'evidence_*.mimes' => "The evidence file must be a PDF, JPG, or PNG.",
                'evidence_*.max' => "The evidence file must not be larger than 2MB."
            ];
    
            $validator = Validator::make($request->all(), $validationRules, $customMessages);
    
            if ($validator->fails()) {
                $errors = $validator->errors();
    
                // Merge duplicate messages for 'evidence' files
                if ($errors->hasAny(array_keys($validationRules))) {
                    $uniqueMessages = collect($errors->all())->unique()->values()->toArray();
                    return redirect()->back()->withErrors($uniqueMessages)->withInput();
                }
    
                return redirect()->back()->withErrors($errors)->withInput();
            }
    
            //$request->validate($validationRules, $customMessages);
            $insertData = [];
            foreach ($user_goals as $goals) {
                $ratingValue = 'rating_' . $goals->id;
                $empremarks = 'remarks_' . $goals->id;
                $fileInputName = 'evidence_' . $goals->id;
                $attachmentPath = null;
    
                if ($request->hasFile($fileInputName)) {
                    $file = $request->file($fileInputName);
                    $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                    $cleanFilename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalFilename);
                    $extension = $file->getClientOriginalExtension();
                    $timestamp = time();
                    $newFilename = $timestamp . '_' . $goals->id . '_' . $cleanFilename . '.' . $extension;
                    $attachmentPath = $file->storeAs('uploads/evidence', $newFilename, 'public');
                }
    
                
    
                DB::table('employee_goal_ratings')->insert([
                    'appraisal_cycle' => $appraisalCycle,
                    'employee_heads_id' => $userHeadsId,
                    'goal_id' => $goals->id,
                    'parats_project_id' => 0,
                    'rating' => $request->input($ratingValue),
                    'employee_comment' => $request->input($empremarks),
                    'appraisal_form_id' => $appraisalFormId,
                    'attachment' => $attachmentPath
                ]);
    
                
            }
    
            DB::table('general_data_by_appraisee')->insert([
                'appraisal_form_id' => $appraisalFormId,
                'appraisal_cycle' => $appraisalCycle,
                'employee_heads_id' => $userHeadsId,
                'key_contributions' => $request->input('key_contribution'),
                'suggestions_for_improvement' => $request->input('appraiser_comment')
            ]);
    
            if ($request->input('action') === 'finalise') {
                DB::table('appraisal_form')
                    ->where('employee_heads_id', $userHeadsId)
                    ->where('id', $appraisalFormId)
                    ->where('appraisal_cycle_id', $appraisalCycle)
                    ->update(['self_finalise' => 1]);
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
            'appraisalMonthType' => $appraisalMonthType,
        ];
        $headers = [
            'X-Api-Key' => env('HEADS_X_API_KEY'),
            'Content-Type' => 'application/json-patch+json'
        ];
        $response = Http::withHeaders($headers)->post($url, $body);
        
        if ($response->failed()) {
            dd("API Request Failed", $response->status(), $response->body());
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
    public function submitEmpGoalsOLd(Request $request)
    {
        //echo '<pre>'; print_r($request->input()); die();
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
    
    public function getEmployeeProjects($userHeadsId,$startDate,$endDate)
    {
        $user_projects_duplicate = DB::table('project_allocations as pa')
                            ->leftJoin('projects as p', 'pa.parats_project_id', '=', 'p.parats_project_id')
                            ->select('p.project_code','p.project_name', 'p.id', 'pa.parats_project_id','pa.allocation_from','pa.allocation_to')
                            ->where('pa.heads_id', $userHeadsId)
                            ->where('p.id', '>', 0)
                            ->where(function ($query) use ($startDate, $endDate) {  
                                $query->whereBetween('pa.allocation_from', [$startDate, $endDate])
                                      ->orWhereBetween('pa.allocation_to', [$startDate, $endDate]);
                            })
                            ->orderBy('p.project_code')
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
                        ->get();
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








}
