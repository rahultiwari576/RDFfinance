<?php

namespace App\Mail;

use App\Models\Loan;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class LoanApplicationMail extends Mailable
{
    use Queueable, SerializesModels;

    public $loan;
    public $user;
    public $bankDetail;
    public $guarantor;
    public $upcomingEMI;
    public $applicationUrl;

    /**
     * Create a new message instance.
     */
    public function __construct(Loan $loan)
    {
        $this->loan = $loan;
        $this->user = $loan->user;
        $this->bankDetail = $loan->bankDetail;
        $this->guarantor = $loan->guarantor;
        
        // Get the first upcoming EMI
        $this->upcomingEMI = $loan->installments()
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->first();
        
        // Generate application URL with username
        $this->applicationUrl = url('/home') . '?username=' . urlencode($this->user->email);
    }

    /**
     * Build the message.
     */
    public function build()
    {
        return $this->subject('Loan Application Submitted - Loan #' . $this->loan->id)
                    ->view('emails.loan-application')
                    ->with([
                        'loan' => $this->loan,
                        'user' => $this->user,
                        'bankDetail' => $this->bankDetail,
                        'guarantor' => $this->guarantor,
                        'upcomingEMI' => $this->upcomingEMI,
                        'applicationUrl' => $this->applicationUrl,
                    ]);
    }
}

