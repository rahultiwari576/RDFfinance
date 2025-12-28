<?php

namespace App\Mail;

use App\Models\LoanInstallment;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Queue\SerializesModels;

class InstallmentReminderMail extends Mailable
{
    use Queueable, SerializesModels;

    public $installment;
    public $user;
    public $loan;

    /**
     * Create a new message instance.
     */
    public function __construct(LoanInstallment $installment)
    {
        $this->installment = $installment;
        $this->user = $installment->loan->user;
        $this->loan = $installment->loan;
    }

    /**
     * Build the message.
     */
    public function build()
    {
        $totalAmount = $this->installment->amount + $this->installment->penalty_amount;
        $isOverdue = $this->installment->status === 'overdue';
        
        return $this->subject('EMI Payment Reminder - Loan #' . $this->loan->id)
                    ->view('emails.installment-reminder')
                    ->with([
                        'installment' => $this->installment,
                        'user' => $this->user,
                        'loan' => $this->loan,
                        'totalAmount' => $totalAmount,
                        'isOverdue' => $isOverdue,
                    ]);
    }
}
