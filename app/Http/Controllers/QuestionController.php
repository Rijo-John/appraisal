<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\AttributeQuestion;
use App\Models\Designation;
use Illuminate\Support\Facades\DB;

class QuestionController extends Controller
{
    public function index()
    {
        $questions = AttributeQuestion::all();
        $designations = Designation::all();
        return view('drag-drop', compact('questions', 'designations'));
    }

    public function saveDesignationQuestions(Request $request)
    {
        $request->validate([
            'designation_id' => 'required|exists:designations,id',
            'question_ids' => 'required|array',
            'question_ids.*' => 'exists:attribute_questions,id',
        ]);

        $designationId = $request->designation_id;
        $questionIds = $request->question_ids;
        //echo '<pre>'; print_r($questionIds); die();
        
        foreach ($questionIds as $questionId) {
            DB::table('attribute_designation_mappings')->updateOrInsert(
                [
                    'designation_id' => $designationId, // ✅ Unique combination
                    'attribute_question_id' => $questionId
                ],
                [
                    'updated_at' => now(), // ✅ Only update if exists
                    'created_at' => now()
                ]
            );
        }
        

        return response()->json(['message' => 'Questions assigned successfully!']);
    }

}
