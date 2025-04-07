<?php

namespace App\Mail;

use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class AppraisalFinalizedNotification extends Mailable
{
    use Queueable, SerializesModels;

    public $employeeEmail;
    public $employeeName;
    public $appraiserOfficerName;
    public $appraisalCycle;
    public $pdfContent;

    public function __construct($employeeEmail, $employeeName, $appraiserOfficerName, $appraisalCycle,$pdfContent)
    {
        $this->employeeEmail = $employeeEmail;
        $this->employeeName = $employeeName; // Store employee name
        $this->appraiserOfficerName = $appraiserOfficerName;
        $this->appraisalCycle = $appraisalCycle;
        $this->pdfContent = $pdfContent;
    }

    public function build()
    {
        return $this->subject('Appraisal Finalized Notification')
                    ->view('emails.appraisal_finalized')
                    ->with([
                        'employeeEmail' => $this->employeeEmail,
                        'employeeName' => $this->employeeName, // Pass to the view
                        'appraiserOfficerName' => $this->appraiserOfficerName,
                        'appraisalCycle' => $this->appraisalCycle
                    ])
                    ->attachData(
                        $this->pdfContent,                        
                        'Appraisal_' . $this->employeeName . '.pdf', 
                        [
                            'mime' => 'application/pdf',
                        ]
                    );
    }
}

