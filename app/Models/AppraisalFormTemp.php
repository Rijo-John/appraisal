<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AppraisalFormTemp extends Model
{
    use HasFactory;
    protected $table = 'appraisal_form_temp';

    public function insertAppraisalForm($data){
        // Delete all existing records
        self::truncate(); 
        
        // Prepare data for insertion
        $insertData = [];
        foreach ($data as $each_appraisal) {
            $insertData[] = [
                'employee_heads_id' => $each_appraisal['EmployeeId'],
                'employee_code' => $each_appraisal['Code'],
                'reporting_officer_heads_id' => $each_appraisal['ReportingOfficerId'],
                'reporting_officer_name' => $each_appraisal['ReportingOfficer'],
                'appraiser_officer_heads_id' => $each_appraisal['AppraiserId'],
                'appraiser_officer_name' => $each_appraisal['AppraiserOfficer'],
                'designation_id' => $each_appraisal['DesignationId'],
                'department_id' => $each_appraisal['DepartmentId'],
                'practise' => $each_appraisal['Practise'],
                'status' => 1,
                'created_at' => now(),
                'updated_at' => now(),
            ];
        }

        // Insert data in bulk
        self::insert($insertData);
    }
}
