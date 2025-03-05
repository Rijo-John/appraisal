<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\AttributeReviewRating;
use Illuminate\Support\Facades\Validator;
class AttributeReviewController extends Controller
{
    public function index()
    {
        $designation = 1;
        $logged_user = session('logged_user_heads_id');
        $appraiser_officer = session('appraiser_officer_heads_id');
        $appraisal_form_id = session('appraisal_form_id');

        if($appraisal_form_id > 0)
        {
            $data = DB::table('attribute_designation_mappings as mapping')
            ->leftJoin('attribute_questions as questions', 'mapping.attribute_question_id', '=', 'questions.id')
            ->leftJoin('attributes', 'questions.attribute_id', '=', 'attributes.id')
            ->leftJoin('attribute_types', 'questions.attribute_type_id', '=', 'attribute_types.id')
            ->where('mapping.designation_id', $designation)
            ->select([
                'attribute_types.id as attribute_type_id',
                'attribute_types.attribute_type',
                'attributes.id as attribute_id',
                'attributes.attribute_name',
                'questions.question as attribute_question',
                'mapping.attribute_question_id'
            ])
            ->get();
            //->paginate(10);
            return view('attribute_review_listing', compact('data'));
        }
        else
        {
            return view('no_permission');
        }
        
    }

    public function saveRatings(Request $request)
    {
        $appraisal_form_id = session('appraisal_form_id');
        if($appraisal_form_id > 0)
        {
            $errorArray = [];
            $validatedData = $request->validate([
                'rating' => 'required|array',
            ], [
                'rating.required' => 'Ratings are required.',
                'rating.array' => 'Invalid rating format.',
            ]);

            foreach ($validatedData['rating'] as $value) {
                if($value != '')
                {
                    $parts = explode("_", $value);
                    if (count($parts) != 2) {
                        array_push($errorArray, "error");
                        return response()->json(['error' => 'Invalid rating format.'], 400);
                    }
            
                }
                else
                {
                    array_push($errorArray, "error");
                    return response()->json(['error' => 'Kindly provide rating for all attributes'], 400);
                }
            }

            if(count($errorArray) == 0)
            {
                foreach ($validatedData['rating'] as $value) {
                    $parts = explode("_", $value);
                    AttributeReviewRating::create([
                        'appraisal_form_id' => $appraisal_form_id,
                        'attribute_qstn_id' => $parts[0],
                        'attribute_rating' => $parts[1],

                    ]);
                }
                return response()->json(['success' => true]);
            }
        }
        else
        {
            return view('no_permission');
        }                 
    }

}
