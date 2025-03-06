<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppraisalForm;
use App\Models\AppraisalCycle;
use Illuminate\Support\Facades\DB;
use DataTables;

class AppraisalMasterController extends Controller
{
    public function getCurrentAppraisalCycle(){
        $appraisalCycleData = AppraisalCycle::where('status', 1)
                ->first();
        return $appraisalCycleData;
    }
    public function getAppraisalData(Request $request)
    {
        $currentAppraisalCycleData = $this->getCurrentAppraisalCycle();
        //dd($currentAppraisalCycleData->id);

        $appraisals = AppraisalForm::select(
                'appraisal_form.id',
                DB::raw("CONCAT(emp.first_name, ' ', emp.last_name) as employee_name"),
                'designations.designation_name as designation',
                DB::raw("CONCAT(rep.first_name, ' ', rep.last_name) as reporting_officer_name"),
                DB::raw("CONCAT(app.first_name, ' ', app.last_name) as appraiser_officer_name")
            )
            ->join('internal_users as emp', 'appraisal_form.employee_heads_id', '=', 'emp.heads_id')
            ->join('designations', 'appraisal_form.designation_id', '=', 'designations.id')
            ->leftJoin('internal_users as rep', 'appraisal_form.reporting_officer_heads_id', '=', 'rep.heads_id')
            ->leftJoin('internal_users as app', 'appraisal_form.appraiser_officer_heads_id', '=', 'app.heads_id')
            ->where('appraisal_form.status', 1)
            ->where('appraisal_cycle_id', $currentAppraisalCycleData->id);
            
            //dd($appraisals);

        return DataTables::of($appraisals)->make(true);
    }
}
