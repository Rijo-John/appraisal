<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppraisalForm;
use App\Models\InternalUser;
use App\Models\AppraisalCycle;
use App\Models\EmployeeGoalRating;
use Illuminate\Support\Facades\DB;
use App\Exports\AppraisalsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Http;
use Carbon\Carbon;

class AppraiserEvaluationNonTechnicalController extends Controller
{
    public function index($id){
        $sessionData = session()->all();
        $appraisalFormId = Crypt::decrypt($id);

        $appraisalFormData = DB::table('appraisal_form')
                            ->where('id', $appraisalFormId)
                            ->where('status', 1)
                            ->first();
                            //dd($appraisalFormData);

        $appraiserOfficerName = $appraisalFormData->appraiser_officer_name;

        $employeeHeadsId = $appraisalFormData->employee_heads_id;

        $user = InternalUser::where('heads_id',$employeeHeadsId)->first();
        
        $appraisalEndDate = $appraisalStartDateYMD = $appraisalEndDateYMD = Carbon::now()->format('Y-m-d');
        //dd();
        $selfFinalise = DB::table('appraisal_form')
                            ->where('id', $appraisalFormId)
                            ->value('self_finalise');

        $appraisalCycleData =  DB::table('appraisal_cycle')
                            ->select('appraisal_cycle','appraisal_period','appraisal_period_start','appraisal_period_end')
                            ->where('id', $appraisalFormData->appraisal_cycle_id)
                            ->where('status', 1)
                            ->first();
        if($appraisalCycleData) {
            if (!empty($appraisalCycleData) && !empty($appraisalCycleData->appraisal_period_start) &&  !empty($appraisalCycleData->appraisal_period_end)) {
                $appraisalStartDateYMD = $appraisalCycleData->appraisal_period_start;
                $appraisalEndDateYMD = $appraisalCycleData->appraisal_period_end;
            }
        }

        $vigyanCourseDetails = [];
        if($appraisalCycleData) {
            if (!empty($appraisalCycleData) && !empty($appraisalCycleData->appraisal_period_start) &&  !empty($appraisalCycleData->appraisal_period_end) && $user->email!='') {
                $appraisalEndDate = $appraisalCycleData->appraisal_period_end;
                $baseUrl = env('VIGYAN_API_URL');
                $params = [
                    'wstoken' => env('VIGYAN_API_TOKEN'),
                    'wsfunction' => env('VIGYAN_API_FUNCTION'),
                    'moodlewsrestformat' => 'json',
                    'start_date' => $appraisalCycleData->appraisal_period_start,
                    'end_date' => $appraisalCycleData->appraisal_period_end,
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
        if (isset($certificationsfromHeads->AppraisalCertListDataResponse) && count($certificationsfromHeads->AppraisalCertListDataResponse) > 0) {
                // Your logic here
        }
        else
        {
            $certificationsfromHeads = new \stdClass();
            $certificationsfromHeads->AppraisalCertListDataResponse = [];
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
            'appraisal_period' => $appraisalCycleData->appraisal_period,
            'appraiserOfficerName' => $appraiserOfficerName,
        ];

        $user_goals =  DB::table('goals')
                        ->select('id','goal','employee_heads_id','appraisal_cycle','weightage')
                        ->where('appraisal_cycle', $appraisalFormData->appraisal_cycle_id)
                        ->where('employee_heads_id', $employeeHeadsId)
                        ->get();

        $user_projects = DB::table('project_allocations')
                            ->select('projects.parats_project_id','projects.project_name')
                            ->leftJoin('projects', 'projects.parats_project_id', '=', 'project_allocations.parats_project_id')
                            ->where('project_allocations.heads_id', $employeeHeadsId)
                            ->get();

        $submitted_goal_ratings =  DB::table('employee_goal_ratings')
                                    ->select('*')
                                    ->where('appraisal_cycle', $appraisalFormData->appraisal_cycle_id)
                                    ->where('employee_heads_id', $employeeHeadsId)
                                    ->get();

        $submittedGeneralData = DB::table('general_data_by_appraisee')
                        ->where('appraisal_cycle', $appraisalFormData->appraisal_cycle_id)
                        ->where('employee_heads_id', $employeeHeadsId)
                        ->where('appraisal_form_id', $appraisalFormId)
                        ->first() ?? (object) ['key_contributions' => '', 'suggestions_for_improvement' => ''];

        $goalWiseData = [];

        foreach ($submitted_goal_ratings as $item) {
            $goalId = $item->goal_id;
            if (!isset($goalWiseData[$goalId])) {
                $goalWiseData[$goalId] = [];
            }
            $goalWiseData[$goalId][] = $item;
        }

        $goalWiseGroupedData = collect($goalWiseData)->mapWithKeys(function ($items, $goalId) {
                return [$goalId => collect($items)];
        });

        return view('appraiserevaluationnontechnical', [
            'employeeData' => $employeeData,
            'appraisalFormId'=>$appraisalFormData->id,
            'user_goals' => $user_goals,
            'user_projects' => $user_projects,
            'goalWiseData' => $goalWiseData,
            'selfFinalise' => $selfFinalise,
            'submittedGeneralData'=>$submittedGeneralData,
            'vigyanCourseDetails' => $vigyanCourseDetails,
            'certificationsfromHeads' => $certificationsfromHeads,
        ]);
        

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

    public function submitAppraiserGoals(Request $request){
        $sessionData = session()->all();
        $appraisalEndDate = $appraisalStartDateYMD = $appraisalEndDateYMD = Carbon::now()->format('Y-m-d');

        $appraisalFormId = $request->input('appraisalFormId');
        $goalRatingIds = $request->input('goalRatingId');
        $appraisalFormData = DB::table('appraisal_form')
                            ->where('id', $appraisalFormId)
                            ->where('status', 1)
                            ->first();
        $employeeHeadsId = $appraisalFormData->employee_heads_id;
        $appraisalCycleId = $appraisalFormData->appraisal_cycle_id;
        $encryptedId = Crypt::encrypt($appraisalFormId);

        foreach ($goalRatingIds as $id) {
            $ratingKey   = 'appraiser_rating_' . $id;
            $remarksKey  = 'appraiser_remarks_' . $id;
            $fileKey     = 'appraiser_evidence_' . $id;

            $existingAttachment = DB::table('employee_goal_ratings')
                            ->where('id', $id)
                            ->value('appraiser_attachment');

            $attachmentPath = $existingAttachment;

            if ($request->hasFile($fileKey)) {
                $validator = Validator::make(
                    $request->all(),
                    [
                        $fileKey => 'mimes:pdf,jpg,jpeg,png|max:2048', // max:2048 means 2MB
                    ],
                    [
                        "$fileKey.mimes" => 'Only PDF, JPG, and PNG files are allowed.',
                        "$fileKey.max" => 'File size must be 2MB or less.',
                    ]
                );

                if ($validator->fails()) {
                    return redirect()->back()
                        ->withErrors($validator)
                        ->withInput();
                }

                $file = $request->file($fileKey);
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $cleanFilename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalFilename);
                $extension = $file->getClientOriginalExtension();
                $timestamp = time();
                $newFilename = $timestamp . '_' . $id . '_' . $cleanFilename . '.' . $extension;
                $attachmentPath = $file->storeAs('uploads/appraiser_evidence', $newFilename, 'public');
            }
            DB::table('employee_goal_ratings')->updateOrInsert(
                [
                    'id' => $id,
                    'employee_heads_id'=>$employeeHeadsId,
                    ''
                ],
                [
                    'appraiser_rating' => $request->input($ratingKey),
                    'appraiser_comment' => $request->input($remarksKey),
                    'appraiser_attachment' => $attachmentPath
                ]
            );

        }

        //return redirect()->route('myappraisal');
        //return redirect()->route('appraiserevaluateindex', ['id' => $encryptedId]);
        return redirect()
            ->route('appraiserevaluateindex', ['id' => $encryptedId])
            ->with('success', 'Appraiser evaluation submitted successfully.');
        
    }
}
