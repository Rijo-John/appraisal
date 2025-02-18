<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Project extends Model
{
    protected $table = 'projects';
  protected $primaryKey = 'id';
   protected $fillable = [
        'parats_project_id',
        'project_name',
       'project_code',
       'project_manager_hrm_id',
        'process_status',
        'project_start_date',
       'project_end_date',
       'customer_name',
       'project_delivery_date',
       'project_reviewer',
       'project_creator',
       'project_category_id',
       't_and_m',
       'project_mq_auditor',
       'practice'
     ];
    use HasFactory;
    public function project()
    {
        return $this->belongsTo(Survey::class, 'parats_project_id');
    }
}
