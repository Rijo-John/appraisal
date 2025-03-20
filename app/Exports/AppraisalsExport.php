<?php

namespace App\Exports;

use App\Models\AppraisalForm;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromCollection;
use Maatwebsite\Excel\Concerns\WithHeadings;

class AppraisalsExport implements FromCollection, WithHeadings
{
    protected $cycleId;

    public function __construct($cycleId)
    {
        $this->cycleId = $cycleId;
    }

    public function collection()
    {
        return AppraisalForm::select(
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
        ->when($this->cycleId, function ($query) {
            return $query->where('appraisal_form.appraisal_cycle_id', $this->cycleId);
        })
        ->get();
    }

    public function headings(): array
    {
        return [
            'Employee Name',
            'Designation',
            'Reporting Officer',
            'Appraiser Officer'
        ];
    }
}
