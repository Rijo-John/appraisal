<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class AttributeReviewRating extends Model
{
    use HasFactory;
    protected $table = 'attribute_review_ratings'; 

    protected $fillable = ['appraisal_form_id', 'attribute_qstn_id', 'attribute_rating'];
}
