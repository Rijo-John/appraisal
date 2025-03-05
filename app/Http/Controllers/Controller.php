<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Routing\Controller as BaseController;
use App\Models\AppraisalCycle;

class Controller extends BaseController
{
    use AuthorizesRequests, ValidatesRequests;

    public function getCurrentAppraisalCycle(){
        $appraisalCycleData = AppraisalCycle::where('status', 1)
                ->first();
        return $appraisalCycleData;
    }
}
