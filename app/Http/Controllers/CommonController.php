<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\User;
use App\Models\InternalUser;
use App\Models\Project;
use App\Models\Designation;
use App\Models\AppraisalFormTemp;
use App\Models\AppraisalCycle;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Illuminate\Support\Facades\Session;

class CommonController extends Controller
{

    public function getCurrentAppraisalCycle(){
        $appraisalCycleData = AppraisalCycle::where('status', 1)
                ->first();
        return $appraisalCycleData;
    }
    public function decryptAppraisalResponse($encryptedData){
        $key = env('APPRAISALUSER_ENCRYPTION_KEY'); // 32-byte key
        $iv = env('APPRAISALUSER_IV'); // 16-byte IV (if required)

        $decrypted = openssl_decrypt(
            base64_decode($encryptedData), // Convert from Base64 if necessary
            'aes-256-cbc', // Encryption algorithm
            $key,
            OPENSSL_RAW_DATA,
            $iv
        );

        return $decrypted;
    }


    public function getSyncedAppraisalUsers()
    {
        $users = AppraisalFormTemp::select(
                                'appraisal_form_temp.id',
                                'appraisal_form_temp.employee_code',
                                'internal_users.username as username',
                                'appraisal_form_temp.reporting_officer_name',
                                'appraisal_form_temp.appraiser_officer_name',
                                'designations.designation_name as designation',
                                'appraisal_form_temp.department_name'
                            )
                    ->leftJoin('internal_users', function ($join) {
                        $join->on('internal_users.heads_id', '=', 'appraisal_form_temp.employee_heads_id')
                             ->where('internal_users.emp_type', 'Permanent');
                    })
                    ->leftJoin('designations', 'designations.designation_id', '=', 'appraisal_form_temp.designation_id')

                    ->orderBy('appraisal_form_temp.employee_heads_id', 'asc')
                    ->get();
        //dd($users);

        return response()->json($users);
    }



    public function syncAppraisalUsers(){
        $url = env('APPRAISALUSERHEADSURL');
        $appraisalFormInstance = new AppraisalFormTemp();
        $currentMonth = Carbon::now()->month;
        $appraisalMonth = ($currentMonth < 7) ? 1 : 2;
        
        $body = [
            'appraisalMonth' => $appraisalMonth
        ];
        
        $headers = [
            'X-Api-Key' => 'RmVzQXAxVXNlcjpGZXNQQXNzd29yQA',
            'Content-Type' => 'application/json',
        ];
        $response = Http::withHeaders($headers)->post($url, $body);
        //$response = Http::withHeaders($headers)->post($url, $payload);
        if ($response->successful()) {
            $data = $response->json();
            $decryptedResponse = json_decode($this->decryptAppraisalResponse($data['response']),true);
            $appraisalFormInstance->insertAppraisalForm($decryptedResponse['AppraisalListDataResponse']);



            $syncedData = AppraisalFormTemp::select(
                                'appraisal_form_temp.employee_code',
                                'internal_users.username as username',
                                'appraisal_form_temp.reporting_officer_name',
                                'appraisal_form_temp.appraiser_officer_name',
                                'designations.designation_name as designation',
                                'appraisal_form_temp.department_name'
                            )
                    ->leftJoin('internal_users', 'internal_users.heads_id', '=', 'appraisal_form_temp.employee_heads_id')
                    ->leftJoin('designations', 'designations.designation_id', '=', 'appraisal_form_temp.designation_id')
                    ->orderBy('appraisal_form_temp.employee_heads_id', 'asc')
                    ->get();

                    //dd($syncedData);

            return response()->json([
                'message' => 'Data synced successfully!',
                'data' => $syncedData
            ]);
            
        }
        return response()->json([
            'message' => 'Error syncing data.'
        ], 500);
    }

    public function syncDesignations()
    {
        $designationInstance = new Designation();
        $url = env('DESIGNATIONSYNCURL');
        $response = Http::post($url);

        if ($response->successful()){
            $data = json_decode($response->body(), true);
            $designationInstance->insertDesignation($data);
            
        }
    }

    
    public function syncUsers()
    {
        $userInstance = new InternalUser();
        $client = new Client();

        // Get URL from .env file
        $url = env('USERSYNCURL');

        // Make a GET request
        $response = $client->get($url);

        // Get the response body as a string
        $body = $response->getBody()->getContents();

        // Decode the JSON response
        $data = json_decode($body, true);
        // Insert users (assuming insertUsers is a method in your InternalUser model)
        $userInstance->insertUsers($data);
    }

    public function syncProjects()
    {
        $url = env('PROJECTSYNCURL');
        $response = Http::get($url);
        if ($response->successful()) {
            $projects = $response->json();
            foreach ($projects['data'] as $project) {
                $projectData = [
                    'parats_project_id' => $project['projectId'] ?? null,
                    'project_name' => $project['project'] ?? null,
                    'project_code' => $project['projectCode'] ?? null,
                    'project_manager_hrm_id' => $project['pmheads_id'] ?? null,
                    'process_status' => $project['processStatus'] ?? null,
                    'project_start_date' => $project['fromDate'] ?? null,
                    'project_end_date' => $project['toDate'] ?? null,
                    'customer_name' => $project['customer_name'] ?? null,
                    'project_delivery_date' => $project['endDate'] ?? null,
                    'practice' => $project['practice'] ?? null,
                    'project_reviewer' => $project['reviewer_headsid'] ?? null,
                    'project_creator' => $project['creator_hedasid'] ?? null,
                    'project_category_id' => $project['category'] ?? null,
                    't_and_m' => $project['tandm'] ?? null,
                    'project_mq_auditor' => $project['Mq_heads_id'] ?? null,
                ];
                if ($projectData['project_delivery_date'] === '0000-00-00') {
                    $projectData['project_delivery_date'] = null;  
                }
                $existingProject = Project::where('parats_project_id', $projectData['parats_project_id'])->first();
                if ($existingProject){
                    $hasChanged=false;
                    foreach($projectData as $key=>$value){
                      if($existingProject->getOriginal($key)!=$value) {
                          $hasChanged = true;
                            break; 
                      }
                    }

                    if($hasChanged){
    //               echo "<pre>";print_R($projectData);exit;
                       $existingProject->update($projectData);
                       Log::info("Project ID {$projectData['parats_project_id']} updated.");
                   }else {
                        Log::info("No changes for Project ID {$projectData['parats_project_id']}.");
                    }
                }
                else {  
                    Project::create($projectData);
                    Log::info("New project inserted with ID {$projectData['parats_project_id']}.");
                }
            }
            DB::table('projects')->insert(
                DB::table('projects_temp as p')
                    ->select('p.*')
                    ->whereNotExists(function ($query) {
                        $query->select(DB::raw(1))
                              ->from('projects_temp as p2')
                              ->whereColumn('p.project_code', 'p2.project_code')
                              ->whereColumn('p2.parats_project_id', '>', 'p.parats_project_id');
                    })
                    ->get()
                    ->map(function ($row) {
                        return (array) $row; // Convert objects to associative arrays for insert
                    })
                    ->toArray()
            );
                    DB::table('projects as p1')
                ->join('projects_temp as p2', 'p1.project_code', '=', 'p2.project_code')
                ->update([
                    'p1.project_start_date' => DB::raw('(SELECT MIN(project_start_date) FROM projects_temp p2 WHERE p1.project_code = p2.project_code)')
                ]);
        }else {
            Log::error('Failed to fetch data: ' . $response->status());
        }
    }

    public function storeAppraisalUsers(Request $request)
    {
        
        $selectedUsers = $request->input('users');

        if (empty($selectedUsers)) {
            return response()->json(['message' => 'No users selected'], 400);
        }

        $users = DB::table('appraisal_form_temp')->whereIn('id', $selectedUsers)->get();
        //dd($users);
        $insertData = $users->map(function ($user) {
            $sessionData = session()->all();
            $currentAppraisalCycle = $sessionData['current_appraisal_cycle'];

            $appraisalCategory=0;
            $appraisalSubCategory=0;

            if (in_array($user->department_id, explode(',', env('NON_TECHNICAL_DEPARTMENT_IDS', '')))) {
                $appraisalCategory = 2;
            }else{
                if (in_array($user->designation_id, explode(',', env('TECHNICAL_DESIGNATION_IDS_OLD', '')))) {
                    $appraisalCategory=3;
                    $appraisalSubCategory=1;

                    if(in_array($user->designation_id, explode(',', env('SE_DESIGNATION_IDS', '')))){
                        $appraisalSubCategory=1;
                    }else if(in_array($user->designation_id, explode(',', env('SSE_DESIGNATION_IDS', '')))){
                        $appraisalSubCategory=2;
                    }else if(in_array($user->designation_id, explode(',', env('TL_DESIGNATION_IDS', '')))){
                        $appraisalSubCategory=3;
                    }else if(in_array($user->designation_id, explode(',', env('TEST_DESIGNATION_IDS', '')))){
                        $appraisalSubCategory=4;
                    }
                }else{
                    $appraisalCategory=1;
                }
            }

            
            return [
                'employee_heads_id' => $user->employee_heads_id,
                'employee_code' => $user->employee_code,
                'reporting_officer_heads_id' => $user->reporting_officer_heads_id,
                'reporting_officer_name' => $user->reporting_officer_name,
                'appraiser_officer_heads_id' => $user->appraiser_officer_heads_id,
                'appraiser_officer_name' => $user->appraiser_officer_name,
                'designation_id' => $user->designation_id,
                'department_id' => $user->department_id,
                'practise' => $user->practise,
                'status'=>0,
                'appraisal_cycle_id' =>$currentAppraisalCycle,
                'appraisal_category' =>$appraisalCategory,
                'appraisal_sub_category' =>$appraisalSubCategory,
                'created_at' => Carbon::now()->toDateTimeString(), 
                'updated_at' => Carbon::now()->toDateTimeString(),
            ];
        })->toArray();
        //dd($insertData);
        DB::table('appraisal_form')->insert($insertData);

        return response()->json(['message' => 'Data successfully inserted into appraisal_form']);

    }


}
