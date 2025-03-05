<?php

namespace App\Services;

use App\Models\AppraisalForm;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Crypt;

class AppraisalFormService
{
    public function getCurrentCycleEmployee()
    {
        $logged_user = session('logged_user_heads_id');
        $results =  DB::table('appraisal_form')
                    ->select(
                        'appraisal_form.id as app_form_id',
                        'appraisal_form.employee_heads_id',
                        'users.id as userid',
                        'users.first_name',
                        'users.last_name',
                        'users.emp_code',
                        'appraisal_form.appraiser_finalise'
                    )
                    ->leftJoin('internal_users as users', 'users.heads_id', '=', 'appraisal_form.employee_heads_id')
                    ->where('appraisal_form.appraiser_officer_heads_id', $logged_user)
                    ->where('appraisal_form.status', 1)
                    ->where('appraisal_form.self_finalise', 1)
                    ->get();


        // Convert results into an array using foreach
        $formattedResults = [];
        foreach ($results as $row) {
            $formattedResults[] = [
                'app_form_id'       => $row->app_form_id,
                'employee_heads_id' => $row->employee_heads_id,
                'full_name'         => $row->first_name . ' ' . $row->last_name,
                'emp_code'          => $row->emp_code,
                'appraiser_finalise'=> $row->appraiser_finalise,
                'encrypteduserId' =>  Crypt::encrypt($row->userid)
            ];
        }

        return $formattedResults;
       

    }
}
