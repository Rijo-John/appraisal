<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AttributeReviewController extends Controller
{
    public function index()
    {
        //$questions = AttributeQuestion::all();
        //$designations = Designation::all();
        

        $data = DB::table('attribute_designation_mappings as mapping')
                ->leftJoin('attribute_questions as questions', 'mapping.attribute_question_id', '=', 'questions.id')
                ->leftJoin('attributes', 'questions.attribute_id', '=', 'attributes.id')
                ->leftJoin('attribute_types', 'questions.attribute_type_id', '=', 'attribute_types.id')
                ->where('mapping.designation_id', 1)
                ->select([
                    'attribute_types.id as attribute_type_id',
                    'attribute_types.attribute_type',
                    'attributes.id as attribute_id',
                    'attributes.attribute_name',
                    'questions.question as attribute_question'
                ])
                ->get();
                //->paginate(10);




        return view('attribute_review_listing', compact('data'));
    }
    public function saveRatings(Request $request)
    {
        echo '<pre>'; print_r($request); die();
        $ratings = $request->input('ratings', []);

        foreach ($ratings as $questionId => $rating) {
            if (!empty($rating)) {
                DB::table('question_ratings')->updateOrInsert(
                    ['question_id' => $questionId],
                    ['rating' => $rating, 'updated_at' => now()]
                );
            }
        }

        return response()->json(['success' => true]);
    }
}
