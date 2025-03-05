<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Services\AppraisalFormService;

class AppraisalFormController extends Controller
{
    protected $appraisalFormService;

    public function __construct(AppraisalFormService $appraisalFormService)
    {
        $this->appraisalFormService = $appraisalFormService;
    }

    public function reviewEmployeeList()
    {
        $employees = $this->appraisalFormService->getCurrentCycleEmployee();
        return view('empolyee_review_listing', compact('employees'));
    }

    
}
