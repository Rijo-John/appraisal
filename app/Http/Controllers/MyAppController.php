<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\AppraisalCycle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;
use Illuminate\Support\Facades\Validator;

class MyAppController extends Controller
{
    
    public function appraisal_project_wise(Request $request){
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
            if($appraisal_form_id == 0 || $appraisal_form_id == '')
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
                        'email' => "aneesh.ks@thinkpalm.com", //$user->email
                    ];
                   
                    $response = Http::withHeaders([
                        'Content-Type' => 'application/json'
                    ])->get($baseUrl, $params);
                    $vigyanData = $response->json(); 
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

                    if(count($vigyanData['vigyan_training_details']) > 0 || count($vigyanData['internal_hours']) > 0 )
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
                    if(count($vigyanData['internal_hours']) > 0) {
                        
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
        $appraiseeSubCategory =  DB::table('appraisal_form')
        ->select('appraisal_sub_category')
        ->where('id', $appraisal_form_id)
        ->where('employee_heads_id', $userHeadsId)
        ->where('status', 1)
        ->get();

        if(count($appraiseeSubCategory) > 0) {
            $subCategory = $appraiseeSubCategory[0]->appraisal_sub_category;
        }
        
        if($appraisal_category == 3 && $subCategory >0) ////////////////////////////////////// By Rijo Project ////////////////////////
        {
            /**
            * Employee Goal details Array 
            * This block of code contains the goal and project details of the logged in user for the current appraisal cycle
            */
                $user_attributes = $this->getProjectReviewQuestions($subCategory);
                $user_projects =  $this->getEmployeeProjects($userHeadsId);
                $submitted_project_ratings =  DB::table('project_review_ratings')
                                            ->select('*')
                                            ->where('appraisal_cycle', $appraisalCycle)
                                            ->where('employee_heads_id', $userHeadsId)
                                            ->where('appraisal_form_id', $appraisal_form_id)
                                            ->get();
                $submitted_project_extra  =  DB::table('project_review_extra')
                                            ->select('*')
                                            ->where('appraisal_cycle', $appraisalCycle)
                                            ->where('employee_heads_id', $userHeadsId)
                                            ->where('appraisal_form_id', $appraisal_form_id)
                                            ->get();
                $submitted_general_data =  DB::table('common_data_by_appraisee')
                                            ->select('*')
                                            ->where('appraisal_cycle', $appraisalCycle)
                                            ->where('employee_heads_id', $userHeadsId)
                                            ->where('appraisal_form_id', $appraisal_form_id)
                                            ->get();
                
                              
                $projectWiseData = [];
                foreach ($submitted_project_ratings as $item) {
                    $projectId = $item->parats_project_id;
                    if (!isset($projectWiseData[$projectId][$item->attribute_id])) {
                        $projectWiseData[$projectId][$item->attribute_id] = [];
                    }
                    $projectWiseData[$projectId][$item->attribute_id][] = $item;
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
            //echo '<pre>'; print_r($vigyanCourseDetails); die();
        
            return view('appraisal_project_wise', [
                'employeeData' => $employeeData,
                'user_attributes' => $user_attributes,
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
    
    public function appraisal_project_wise_submit(Request $request)
    {
        
        $sessionData = session()->all();
        $appraiserOfficerName = $sessionData['appraiserOfficerName'];
        $appraisal_category = $sessionData['appraisal_category'];  
        $userHeadsId = $sessionData['logged_user_heads_id'];
        $appraisalCycle = $sessionData['current_appraisal_cycle'];
        $appraisal_form_id = $sessionData['appraisal_form_id'];
        $appraisalEndDate = $appraisalStartDateYMD = $appraisalEndDateYMD = Carbon::now()->format('Y-m-d');
        
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
        $appraiseeSubCategory =  DB::table('appraisal_form')
        ->select('appraisal_sub_category','self_finalise')
        ->where('id', $appraisal_form_id)
        ->where('employee_heads_id', $userHeadsId)
        ->where('status', 1)
        ->get();
        $subCategory = 0;
        $self_finalise = 1;
        if(count($appraiseeSubCategory) > 0) {
            $subCategory = $appraiseeSubCategory[0]->appraisal_sub_category;
            $self_finalise = $appraiseeSubCategory[0]->self_finalise;
        }

        if($appraisal_category == 3 && $subCategory> 0 && $self_finalise == 0)  /////////////////   Code by Rijo ////////////////////////////
        {
            $user_projects =  $this->getEmployeeProjects($userHeadsId);
            $user_attributes = $this->getProjectReviewQuestions($subCategory);
            if ($request->input('is_finalise') == '1') {
                foreach ($user_projects as $projects)
                {
                    $i = 1;
                    foreach($user_attributes as $attribute)
                    {
                        $ratingValue = 'rating_' . $projects->parats_project_id . '_' . $attribute->id;
                        $validationRules[$ratingValue] = 'required|in:1,2,3,4';

                         // Custom error message with index
                        $customMessages["$ratingValue.required"] = "All Rating is required for Project #".$projects->project_name. " - Attribute #".$i;
                        $customMessages["$ratingValue.in"] = "Invalid rating selected for Project #".$projects->project_name. " - Attribute #".$i;
                        $i++;
                    }
                }
                $validator = Validator::make($request->all(), $validationRules, $customMessages);
            }
            if ($validator->fails()) {
                return redirect()->back()->withErrors($validator)->withInput();
            }
            /**
            * Delete goals rating and key contributions from table.
            * Here, we will check whether goals rating and key contributions are already inserted if yes delete in case of updation. 
            */

                $this->deleteProjectSelfRatingData($appraisalCycle,$userHeadsId,$appraisal_form_id);

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
                    DB::table('project_review_extra')->insert([
                        'appraisal_cycle' => $appraisalCycle,
                        'appraisal_form_id' => $appraisal_form_id,
                        'employee_heads_id' => $userHeadsId,
                        'parats_project_id' => $projects->parats_project_id,
                        'task_details' => $request->input($taskdetails)
                    ]);
                
                    
                    foreach($user_attributes as $attribute)
                    {
                        $ratingValue = 'rating_' . $projects->parats_project_id . '_' . $attribute->id;
                        $empremarks = 'remarks_' . $projects->parats_project_id . '_' . $attribute->id;

                        DB::table('project_review_ratings')->insert([
                            'appraisal_cycle' => $appraisalCycle,
                            'appraisal_form_id'  => $appraisal_form_id,
                            'employee_heads_id' => $userHeadsId,
                            'attribute_id' => $attribute->id,
                            'parats_project_id' => $projects->parats_project_id,
                            'rating' => $request->input($ratingValue),
                            'employee_comment' => $request->input($empremarks)
                        ]);

                    }
                    
                }

            /**
            * Code Ends Here
            */


            /**
            * Insert general goals rating 
            * This block of code insert  the goal and rating without any project
            */
            
                foreach($user_attributes as $attr)
                {
                    $goalRatingDataOnce = [];
                    $ratingValue = 'general_rating_'  . $attr->id;
                    $empremarks = 'general_remarks_' . $attr->id;
                    DB::table('project_review_ratings')->insert([
                        'appraisal_cycle' => $appraisalCycle,
                        'appraisal_form_id'  => $appraisal_form_id,
                        'employee_heads_id' => $userHeadsId,
                        'attribute_id' => $attr->id,
                        'parats_project_id' => -1,
                        'rating' => $request->input($ratingValue),
                        'employee_comment' => $request->input($empremarks)
                    ]);
                }

                DB::table('project_review_extra')->insert([
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
                DB::table('common_data_by_appraisee')->insert([
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
        
        
        return redirect()->route('myapp');

    }

    public function deleteProjectSelfRatingData($appraisalCycle,$userHeadsId,$appraisal_form_id)
    {
        $submittedGoalRatings = DB::table('project_review_ratings')
            ->where('appraisal_cycle', $appraisalCycle)
            ->where('employee_heads_id', $userHeadsId)
            ->where('appraisal_form_id', $appraisal_form_id)
            ->exists(); 
        $projectExtraDetails  = DB::table('project_review_extra')
            ->where('appraisal_cycle', $appraisalCycle)
            ->where('employee_heads_id', $userHeadsId)
            ->where('appraisal_form_id', $appraisal_form_id)
            ->exists(); 
        $generalData  = DB::table('common_data_by_appraisee')
            ->where('appraisal_cycle', $appraisalCycle)
            ->where('employee_heads_id', $userHeadsId)
            ->where('appraisal_form_id', $appraisal_form_id)
            ->exists(); 
        if ($submittedGoalRatings) {
            DB::table('project_review_ratings')
                ->where('appraisal_cycle', $appraisalCycle)
                ->where('employee_heads_id', $userHeadsId)
                ->where('appraisal_form_id', $appraisal_form_id)
                ->delete();
        }
        if ($projectExtraDetails) {
            DB::table('project_review_extra')
                ->where('appraisal_cycle', $appraisalCycle)
                ->where('employee_heads_id', $userHeadsId)
                ->where('appraisal_form_id', $appraisal_form_id)
                ->delete();
        }
        if ($generalData) {
            DB::table('common_data_by_appraisee')
                ->where('appraisal_cycle', $appraisalCycle)
                ->where('employee_heads_id', $userHeadsId)
                ->where('appraisal_form_id', $appraisal_form_id)
                ->delete();
        }

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

    public function getProjectReviewQuestions($subCategory){
        $userattributes =  DB::table('project_review_questions')
        ->select('id','attribute','appraisee_category')
        ->where('appraisee_category', $subCategory)
        ->get();
        return $userattributes;
    }

    public function getEmployeeProjects($userHeadsId) {
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

}
