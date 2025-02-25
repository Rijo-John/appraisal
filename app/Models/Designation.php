<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Designation extends Model
{
    use HasFactory;
    protected $table = 'designations';

    public function checkExistDesignation($designationDetails){
        
        return Designation::where('designation_id', $designationDetails['DesiginationXId'])->exists();
    }

    public function insertDesignation($data){
        foreach ($data['DesInfo'] as $each_designation){
            $isExistDesignation = $this->checkExistDesignation($each_designation);
            if ($isExistDesignation) {
                $update_array = array(
                    'designation_id' =>$each_designation['DesiginationXId'],
                    'designation_name'=>$each_designation['Designation'],
                                                    
                );

                Designation::where('designation_id', $each_designation['DesiginationXId'])->update($update_array);
            }else{
                $insert_array = array(
                    'designation_id' =>$each_designation['DesiginationXId'], 
                    'designation_name'=>$each_designation['Designation'],
                    

                );
                Designation::insert($insert_array);
            }
        }
    }
}
