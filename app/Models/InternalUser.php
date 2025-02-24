<?php

namespace App\Models;

use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Auth\Authenticatable as AuthenticatableTrait;
use Illuminate\Support\Facades\DB;

class InternalUser extends Model implements Authenticatable
{
    use AuthenticatableTrait;
    use HasFactory;
    
    protected $table = 'internal_users';


    public function checkExistEmployee($employDetails){
        
        return InternalUser::where('username', $employDetails['UserName'])->exists();
    }
    
    public function insertUsers($employees){
        foreach ($employees as $each_employee){
            $isExistEmployee = $this->checkExistEmployee($each_employee);
            if ($isExistEmployee) {
                $update_array = array(
                    'heads_id' =>$each_employee['EmployeeId'],
                    'first_name'=>$each_employee['FirstName'],
                    'last_name'=>$each_employee['LastName'],
                    'username'=>$each_employee['UserName'],
                    'email'=>$each_employee['Email'],
                    'emp_code' => $each_employee['Code'],
                    'location' => $each_employee['Location'],
                    'emp_type' => $each_employee['EmployeeType'],
                    'status' => $each_employee['EmployeeStatus'],
                    'profile_pic' => $each_employee['PhotoFileName'],
                    'reporting_officer_id' => $each_employee['ReportingOfficerXId'],
                    'reporting_officer_name' => $each_employee['ReportingOfficer'],
                    'designation_id' => $each_employee['DesignationId'], 
                    'designation_name' => $each_employee['DesignationName'],                                
                );

                
                InternalUser::where('username', $each_employee['UserName'])->update($update_array);


            }else{
                $insert_array = array(
                    'heads_id' =>$each_employee['EmployeeId'], 
                    'first_name'=>$each_employee['FirstName'],
                    'last_name'=>$each_employee['LastName'],
                    'username'=>$each_employee['UserName'],
                    'email'=>$each_employee['Email'],
                    'emp_code' => $each_employee['Code'],
                    'location' => $each_employee['Location'],
                    'emp_type' => $each_employee['EmployeeType'],
                    'status' => $each_employee['EmployeeStatus'],
                    'profile_pic' => $each_employee['PhotoFileName'] ,
                    'reporting_officer_id' => $each_employee['ReportingOfficerXId'],
                    'reporting_officer_name' => $each_employee['ReportingOfficer'],
                    'designation_id' => $each_employee['DesignationId'], 
                    'designation_name' => $each_employee['DesignationName'],

                );

                InternalUser::insert($insert_array);
                
                    
            }
            
           
        }
        
    }
}
