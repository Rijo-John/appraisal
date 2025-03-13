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
        $appraiserOfficerName = $sessionData['appraiserOfficerName'];
        $appraisalData = AppraisalCycle::select('appraisal_period')->where('status',1)->first();
        $user = Auth::user();
        $userHeadsId = $sessionData['logged_user_heads_id'];
        $appraisalCycle = $sessionData['current_appraisal_cycle'];
        $appraisal_form_id = $sessionData['appraisal_form_id'];
        /**
        * Grant access to the employee for the appraisal form. 
        * Here, we will check if the logged-in user is added to the current appraisal cycle.
        */
        if($appraisal_form_id == 0)
        {
            return view('user_not_in_appraisal');
        }

        /**
        * Employee details Array 
        * This block of code contains the self appraisal logged in user details
        */
            $employeeData = [
                'profile_pic' => $user->profile_pic,
                'name'  => $user->first_name . ' ' . $user->last_name,
                'emp_code' => $user->emp_code,
                'designation_name' => $user->designation_name,
                'date_of_join' => $user->date_of_join,
                'appraisal_period' => $appraisalData->profile_pic,
                'appraiserOfficerName' => $appraiserOfficerName
            ];
        /**
        * 
        * Code Ends Here
        *
        */

        /**
        * Employee Goal details Array 
        * This block of code contains the goal and project details of the logged in user for the current appraisal cycle
        */
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
            $goalWiseData = [];
            foreach ($submitted_goal_ratings as $item) {
                $goalId = $item->goal_id;
                if (!isset($goalWiseData[$goalId])) {
                    $goalWiseData[$goalId] = [];
                }
                $goalWiseData[$goalId][] = $item;
            }
        /**
        * 
        * Code Ends Here
        *
        */
        //dd($goalWiseData);

        //return view('my_appraisal', compact('user','appraisalData','appraiserOfficerName','user_goals', 'user_projects', 'goalWiseData'));
        return view('my_appraisal', [
            'employeeData' => $employeeData,
            'user_goals' => $user_goals,
            'user_projects' => $user_projects,
            'goalWiseData' => $goalWiseData,
        ]);
    }
    /* 
    @author ['sooraj r']
    @method ['insert data in to the employee_goal_ratings  according to the goal']
    */

   
    public function submitEmpGoalsNonTechnical(Request $request){
        $sessionData = session()->all();
        $appraiserOfficerName = $sessionData['appraiserOfficerName'];
        $userHeadsId = $sessionData['logged_user_heads_id'];
        $appraisalCycle = $sessionData['current_appraisal_cycle'];

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

        $user_goals = DB::table('goals')
            ->select('id', 'goal', 'employee_heads_id', 'appraisal_cycle', 'weightage')
            ->where('appraisal_cycle', $appraisalCycle)
            ->where('employee_heads_id', $userHeadsId)
            ->get();

        $appraisalFormId = session('appraisal_form_id');
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

        return redirect()->route('myappraisalnontechnical')->with('success', 'Goals submitted successfully.');
    }

}
