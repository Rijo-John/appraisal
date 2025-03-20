<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AppraisalForm;
use App\Models\InternalUser;
use App\Models\AppraisalCycle;
use Illuminate\Support\Facades\DB;
use App\Exports\AppraisalsExport;
use Maatwebsite\Excel\Facades\Excel;
use Illuminate\Support\Facades\Session;
use App\Models\EmployeeGoalRating;
use Illuminate\Support\Facades\Mail;
use App\Mail\AppraisalNotification;
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
        $appraisalCycles = AppraisalCycle::all();
        $appraisals = AppraisalForm::select(
                'appraisal_form.id',
                DB::raw("CONCAT(emp.first_name, ' ', emp.last_name) as employee_name"),
                'designations.designation_name as designation',
                DB::raw("CONCAT(rep.first_name, ' ', rep.last_name) as reporting_officer_name"),
                DB::raw("CONCAT(app.first_name, ' ', app.last_name) as appraiser_officer_name")
            )
            
            ->join('internal_users as emp', function ($join) {
                $join->on('appraisal_form.employee_heads_id', '=', 'emp.heads_id')
                     ->where('emp.emp_type','!=', 'Contract'); // Add additional condition here
            })
            ->join('designations', 'appraisal_form.designation_id', '=', 'designations.id')
            ->leftJoin('internal_users as rep', function ($join) {
                $join->on('appraisal_form.reporting_officer_heads_id', '=', 'rep.heads_id')
                     ->where('rep.emp_type','!=', 'Contract'); 
            })
            ->leftJoin('internal_users as app', function ($join) {
                $join->on('appraisal_form.appraiser_officer_heads_id', '=', 'app.heads_id')
                     ->where('app.emp_type', '!=', 'Contract'); 
            })
            
            ->where('appraisal_cycle_id', $currentAppraisalCycleData->id)
            ->orderBy('emp.first_name', 'asc')
            ->paginate(10);
            
        $appraisalsNoGoals = AppraisalForm::join('internal_users as emp', function ($join) {
                    $join->on('appraisal_form.employee_heads_id', '=', 'emp.heads_id')
                         ->where('emp.emp_type', '!=', 'Contract');
                })
                ->leftJoin('goals', 'goals.employee_heads_id', '=', 'appraisal_form.employee_heads_id')
                ->whereNull('goals.id')
                ->where('appraisal_form.status', 1)
                ->where('appraisal_cycle_id', $currentAppraisalCycleData->id)
                ->select(DB::raw("CONCAT(emp.first_name, ' ', emp.last_name) as employee_name"))
                ->get()
                ->pluck('employee_name') // Now safely pluck the alias
                ->implode(', ');
        

    return view('appraisal_master', compact('appraisals','appraisalsNoGoals','appraisalCycles'));

    }

    public function filterAppraisalsByCycle(Request $request)
    {
        $cycleId = $request->appraisal_cycle_id;

        if (!$cycleId) {
            return response()->json(['error' => 'Invalid appraisal cycle ID'], 400);
        }

        $appraisals = AppraisalForm::select(
                'appraisal_form.id',
                DB::raw("CONCAT(emp.first_name, ' ', emp.last_name) as employee_name"),
                'designations.designation_name as designation',
                DB::raw("CONCAT(rep.first_name, ' ', rep.last_name) as reporting_officer_name"),
                DB::raw("CONCAT(app.first_name, ' ', app.last_name) as appraiser_officer_name")
            )
            ->join('internal_users as emp', function ($join) {
                $join->on('appraisal_form.employee_heads_id', '=', 'emp.heads_id')
                    ->where('emp.emp_type', '!=', 'Contract');
            })
            ->join('designations', 'appraisal_form.designation_id', '=', 'designations.id')
            ->leftJoin('internal_users as rep', function ($join) {
                $join->on('appraisal_form.reporting_officer_heads_id', '=', 'rep.heads_id')
                    ->where('rep.emp_type', '!=', 'Contract');
            })
            ->leftJoin('internal_users as app', function ($join) {
                $join->on('appraisal_form.appraiser_officer_heads_id', '=', 'app.heads_id')
                    ->where('app.emp_type', '!=', 'Contract');
            })
            ->where('appraisal_form.status', 1)
            ->where('appraisal_form.appraisal_cycle_id', $cycleId)
            ->get();

        return response()->json(['appraisals' => $appraisals]);
    }

    public function exportAppraisalsToExcel(Request $request)
    {
        $cycleId = $request->input('appraisal_cycle_id');
        return Excel::download(new AppraisalsExport($cycleId), 'appraisals.xlsx');
    }
    public function deleteAppraisal($id)
    {
        $sessionData = session()->all();
        $appraisal = AppraisalForm::find($id);

        if (!$appraisal) {
            return response()->json(['message' => 'Appraisal not found'], 404);
        }
        $employeeHeadsId = $appraisal->employee_heads_id;
        $appraisalCycle = $sessionData['current_appraisal_cycle'];

        $existingData = EmployeeGoalRating::where('employee_heads_id', $employeeHeadsId)
                                      ->where('appraisal_cycle', $appraisalCycle)
                                      ->exists();     
        if ($existingData) {
            return response()->json(['message' => 'Cannot delete appraisal. Employee goal ratings exist for this cycle.'], 400);
        }

        // Delete the appraisal record
        $appraisal->update(['status' => 0]);

        return response()->json(['message' => 'Appraisal deleted successfully,Now the user is unable to submit the appraisal'], 200);
    }

    public function edit($id)
    {
        $appraisal = AppraisalForm::findOrFail($id);

        $internalUsers = DB::table('internal_users')
                ->select(DB::raw("CONCAT(first_name, ' ', last_name) AS full_name"), 'heads_id')
                ->where('status', 'Active')
                ->where('emp_type', 'Permanent')
                ->get();
        //dd($appraisal);
        return view('appraisaledit', compact('appraisal', 'internalUsers'));
    }

    public function update(Request $request, $id)
    {
        //dd($request->all());
        $appraisal = AppraisalForm::findOrFail($id);
        $appraisal->update($request->all());

        return redirect()->route('appraisaldata')->with('success', 'Appraisal updated successfully!');
    }

    public function getAppraisalUsersContent(Request $request){
        $appraisalCycleData = AppraisalCycle::where('status', 1)
                ->select('id')
                ->first();
        if (!$appraisalCycleData) {
            return response()->json(['message' => 'No active appraisal cycle found'], 404);
        }
        $currentAppraisalCycle = $appraisalCycleData->id;

        $users = InternalUser::leftJoin('appraisal_form', 'internal_users.heads_id', '=', 'appraisal_form.employee_heads_id')
                ->where('appraisal_form.status', 0)
                ->where('appraisal_form.self_finalise', 0)
                ->where('appraisal_form.appraisal_cycle_id', $currentAppraisalCycle)
                ->where('appraisal_form.appraiser_finalise', 0)
                ->select(
                    DB::raw("CONCAT(internal_users.first_name, ' ', internal_users.last_name) AS full_name"),
                    'internal_users.email',
                    'internal_users.emp_code',
                    'appraisal_form.id as appraisal_form_id'
                )
                ->orderBy('internal_users.first_name', 'asc')
                ->get();

        return response()->json($users);

    }

    public function sendAppraisalEmails(Request $request) {
        $users = $request->input('users');
        //dd($users);
        $ccEmails = explode(',', env('APPRAISAL_START_MAIL_CC_ADDRESSES', ''));

        foreach ($users as $user) {
            // Send email using Mailable class
            Mail::to($user['email'])
                ->cc($ccEmails)
                ->send(new AppraisalNotification($user));

            // Update status in appraisal_form table
            AppraisalForm::where('id', $user['appraisal_form_id'])->update(['status' => 1]);
        }

        return response()->json(['success' => true, 'message' => 'Emails sent and status updated']);
    }
}
