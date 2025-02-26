<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use App\Models\User;
use App\Models\InternalUser;
use App\Models\Project;
use App\Models\Designation;
use App\Models\AppraisalForm;
use Illuminate\Support\Facades\Http; 
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class CommonController extends Controller
{
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


    public function syncAppraisalUsers(){
        $url = env('APPRAISALUSERHEADSURL');
        $appraisalFormInstance = new AppraisalForm();
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
            //dd($decryptedResponse['AppraisalListDataResponse']);
            $appraisalFormInstance->insertAppraisalForm($decryptedResponse['AppraisalListDataResponse']);
            
        }
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

}
