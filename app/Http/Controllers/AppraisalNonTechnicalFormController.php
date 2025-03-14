<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AppraisalFormService;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Models\AppraisalCycle;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;


class AppraisalNonTechnicalFormController extends Controller
{
   public function index(Request $request){
        $sessionData = session()->all();
        //dd($sessionData);
        $appraiserOfficerName = $sessionData['appraiserOfficerName'];
        $appraisalData = AppraisalCycle::select('appraisal_period')->where('status',1)->first();
        $user = Auth::user();
        $userHeadsId = $sessionData['logged_user_heads_id'];
        $appraisalCycle = $sessionData['current_appraisal_cycle'];
        $appraisal_form_id = $sessionData['appraisal_form_id'];
        
        if($appraisal_form_id == 0)
        {
            return view('user_not_in_appraisal');
        }

        // Fetch self_finalise status from the appraisal_form table
        $selfFinalise = DB::table('appraisal_form')
            ->where('employee_heads_id', $userHeadsId)
            ->where('id', $appraisal_form_id)
            ->where('appraisal_cycle_id', $appraisalCycle)
            ->value('self_finalise');

        
        $employeeData = [
            'profile_pic' => $user->profile_pic,
            'name'  => $user->first_name . ' ' . $user->last_name,
            'emp_code' => $user->emp_code,
            'designation_name' => $user->designation_name,
            'date_of_join' => $user->date_of_join,
            'appraisal_period' => $appraisalData->profile_pic,
            'appraiserOfficerName' => $appraiserOfficerName
        ];
        

        
            $user_goals =  DB::table('goals')
                            ->select('id','goal','employee_heads_id','appraisal_cycle','weightage')
                            ->where('appraisal_cycle', $appraisalCycle)
                            ->where('employee_heads_id', $userHeadsId)
                            ->get();

            $user_projects = DB::table('project_allocations')
                            ->select('projects.parats_project_id','projects.project_name')
                            ->leftJoin('projects', 'projects.parats_project_id', '=', 'project_allocations.parats_project_id')
                            ->where('project_allocations.heads_id', $userHeadsId)
                            ->get();

            $submitted_goal_ratings =  DB::table('employee_goal_ratings')
                                    ->select('*')
                                    ->where('appraisal_cycle', $appraisalCycle)
                                    ->where('employee_heads_id', $userHeadsId)
                                    ->get();
            $submittedGeneralData = DB::table('general_data_by_appraisee')
                        ->where('appraisal_cycle', $appraisalCycle)
                        ->where('employee_heads_id', $userHeadsId)
                        ->where('appraisal_form_id', $appraisal_form_id)
                        ->first();
            $goalWiseData = [];
            foreach ($submitted_goal_ratings as $item) {
                $goalId = $item->goal_id;
                if (!isset($goalWiseData[$goalId])) {
                    $goalWiseData[$goalId] = [];
                }
                $goalWiseData[$goalId][] = $item;
            }
       
        return view('my_appraisal', [
            'employeeData' => $employeeData,
            'user_goals' => $user_goals,
            'user_projects' => $user_projects,
            'goalWiseData' => $goalWiseData,
            'selfFinalise' => $selfFinalise,
            'submittedGeneralData'=>$submittedGeneralData
        ]);
    }
    /* 
    @author ['sooraj r']
    @method ['insert data in to the employee_goal_ratings  according to the goal']
    */

   
    public function submitEmpGoalsNonTechnical(Request $request){
        //dd($request->all());
        $sessionData = session()->all();
       // dd($sessionData);
        $appraiserOfficerName = $sessionData['appraiserOfficerName'];
        $userHeadsId = $sessionData['logged_user_heads_id'];
        $appraisalCycle = $sessionData['current_appraisal_cycle'];
        $appraisalFormId = session('appraisal_form_id');

        $isFinalised = DB::table('appraisal_form')
                ->where('employee_heads_id', $userHeadsId)
                ->where('id', $appraisalFormId)
                ->where('appraisal_cycle_id', $appraisalCycle)
                ->value('self_finalise');
        if ($isFinalised == 1) {
            return redirect()->back()->withErrors(['error' => 'You cannot submit goals as they have already been finalised.']);
        }

        $submittedGoalRatings = DB::table('employee_goal_ratings')
            ->where('appraisal_cycle', $appraisalCycle)
        ->where('employee_heads_id', $userHeadsId)
        ->exists();


        if ($submittedGoalRatings) {
            DB::table('employee_goal_ratings')
                ->where('appraisal_cycle', $appraisalCycle)
                ->where('employee_heads_id', $userHeadsId)
                ->delete();

        }

        $submittedGeneralData = DB::table('general_data_by_appraisee')
            ->where('appraisal_cycle', $appraisalCycle)
            ->where('employee_heads_id', $userHeadsId)
            ->where('appraisal_form_id', $appraisalFormId)
            ->exists();
        if($submittedGeneralData){
            DB::table('general_data_by_appraisee')
                ->where('appraisal_cycle', $appraisalCycle)
                ->where('employee_heads_id', $userHeadsId)
                ->where('appraisal_form_id', $appraisalFormId)
                ->delete();
        }

        $user_goals = DB::table('goals')
            ->select('id', 'goal', 'employee_heads_id', 'appraisal_cycle', 'weightage')
            ->where('appraisal_cycle', $appraisalCycle)
            ->where('employee_heads_id', $userHeadsId)
            ->get();

        
        $validationRules = [];
        $customMessages = [];

        // **Check if the user clicked "Finalise"**
        if ($request->input('action') === 'finalise') {
            foreach ($user_goals as $goals) {
                $ratingValue = 'rating_' . $goals->id;
                $validationRules[$ratingValue] = 'required|in:0,1,5,10'; 

                $customMessages["$ratingValue.required"] = "Rating is required for  goals.";
                $customMessages["$ratingValue.in"] = "Invalid rating selected for goal.";
            }
        }

        foreach ($user_goals as $goals) {
            $fileInputName = 'evidence_' . $goals->id;
            $validationRules[$fileInputName] = 'nullable|file|mimes:pdf,jpg,png|max:2048';
        }

        $customMessages = [
            'evidence_*.mimes' => "The evidence file must be a PDF, JPG, or PNG.",
            'evidence_*.max' => "The evidence file must not be larger than 2MB."
        ];

        $validator = Validator::make($request->all(), $validationRules, $customMessages);

        if ($validator->fails()) {
            $errors = $validator->errors();

            // Merge duplicate messages for 'evidence' files
            if ($errors->hasAny(array_keys($validationRules))) {
                $uniqueMessages = collect($errors->all())->unique()->values()->toArray();
                return redirect()->back()->withErrors($uniqueMessages)->withInput();
            }

            return redirect()->back()->withErrors($errors)->withInput();
        }

        //$request->validate($validationRules, $customMessages);
        $insertData = [];
        foreach ($user_goals as $goals) {
            $ratingValue = 'rating_' . $goals->id;
            $empremarks = 'remarks_' . $goals->id;
            $fileInputName = 'evidence_' . $goals->id;
            $attachmentPath = null;

            if ($request->hasFile($fileInputName)) {
                $file = $request->file($fileInputName);
                $originalFilename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
                $cleanFilename = preg_replace('/[^A-Za-z0-9_\-]/', '_', $originalFilename);
                $extension = $file->getClientOriginalExtension();
                $timestamp = time();
                $newFilename = $timestamp . '_' . $goals->id . '_' . $cleanFilename . '.' . $extension;
                $attachmentPath = $file->storeAs('uploads/evidence', $newFilename, 'public');
            }

            

            DB::table('employee_goal_ratings')->insert([
                'appraisal_cycle' => $appraisalCycle,
                'employee_heads_id' => $userHeadsId,
                'goal_id' => $goals->id,
                'parats_project_id' => 0,
                'rating' => $request->input($ratingValue),
                'employee_comment' => $request->input($empremarks),
                'appraisal_form_id' => $appraisalFormId,
                'attachment' => $attachmentPath
            ]);

            
        }

        DB::table('general_data_by_appraisee')->insert([
            'appraisal_form_id' => $appraisalFormId,
            'appraisal_cycle' => $appraisalCycle,
            'employee_heads_id' => $userHeadsId,
            'key_contributions' => $request->input('key_contribution'),
            'suggestions_for_improvement' => $request->input('appraiser_comment')
        ]);

        if ($request->input('action') === 'finalise') {
            DB::table('appraisal_form')
                ->where('employee_heads_id', $userHeadsId)
                ->where('id', $appraisalFormId)
                ->where('appraisal_cycle_id', $appraisalCycle)
                ->update(['self_finalise' => 1]);
        }

        return redirect()->route('myappraisalnontechnical')->with('success', 'Goals submitted successfully.');
    }

}
