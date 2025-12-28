<?php

namespace App\Services;

use App\Models\Loan;
use App\Models\LoanInstallment;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class LoanService
{
    public function createLoan(User $user, array $data): Loan
    {
        return DB::transaction(function () use ($user, $data) {
            $calculation = $this->calculateEmi($data);

            $loan = $user->loans()->create([
                'principal_amount' => $calculation['principal_amount'],
                'interest_rate' => $calculation['interest_rate'],
                'tenure_months' => $calculation['tenure_months'],
                'emi_amount' => $calculation['emi_amount'],
                'total_repayment' => $calculation['total_repayment'],
                'status' => 'active',
                'next_due_date' => Carbon::parse($calculation['schedule'][0]['due_date']),
                'penalty_amount' => config('loan.default_penalty', 100),
                'custom_penalty_amount' => $data['custom_penalty_amount'] ?? null,
            ]);

            $this->createInstallments($loan, $calculation['schedule'], $data['custom_penalty_amount'] ?? null);

            return $loan->load('installments');
        });
    }

    public function calculateEmi(array $data): array
    {
        $principal = (float) $data['principal_amount'];
        $rate = (float) $data['interest_rate'] / 12 / 100;
        $tenure = (int) $data['tenure_months'];

        if ($rate === 0.0) {
            $emi = $principal / $tenure;
        } else {
            $emi = $principal * $rate * pow(1 + $rate, $tenure) / (pow(1 + $rate, $tenure) - 1);
        }

        $emi = round($emi, 2);
        $totalRepayment = round($emi * $tenure, 2);
        $schedule = [];
        $balance = $totalRepayment;
        
        // Start from next month, set due date to 10th of each month
        $dueDate = Carbon::now()->addMonth()->day(10);
        
        // If today is past the 10th of current month, start from next month's 10th
        if (Carbon::now()->day > 10) {
            $dueDate = Carbon::now()->addMonth()->day(10);
        } else {
            // If today is before 10th, first EMI is 10th of next month
            $dueDate = Carbon::now()->addMonth()->day(10);
        }

        for ($i = 1; $i <= $tenure; $i++) {
            $schedule[] = [
                'installment_number' => $i,
                'due_date' => $dueDate->copy(),
                'amount' => $emi,
            ];
            // Move to 10th of next month
            $dueDate = $dueDate->copy()->addMonth()->day(10);
            $balance -= $emi;
        }

        return [
            'principal_amount' => $principal,
            'interest_rate' => (float) $data['interest_rate'],
            'tenure_months' => $tenure,
            'emi_amount' => $emi,
            'total_repayment' => $totalRepayment,
            'schedule' => collect($schedule)->map(function ($item) {
                $item['due_date'] = $item['due_date']->format('Y-m-d');
                return $item;
            })->toArray(),
        ];
    }

    public function createInstallments(Loan $loan, array $schedule, ?float $customPenalty = null): void
    {
        foreach ($schedule as $item) {
            // Ensure due date is set to 10th of the month
            $dueDate = Carbon::parse($item['due_date']);
            $dueDate->day(10);
            
            $loan->installments()->create([
                'due_date' => $dueDate,
                'amount' => $item['amount'],
                'status' => 'pending',
                'penalty_amount' => $customPenalty ?? 593, // Default bouncing charge
            ]);
        }
    }

    public function listLoans(User $user): array
    {
        return $user->loans()
            ->with(['installments' => function ($query) {
                $query->orderBy('due_date');
            }])
            ->latest()
            ->get()
            ->map(function (Loan $loan) {
                // Get only 1 upcoming EMI with bouncing charge logic
                $upcomingEMIs = $this->getUpcomingEMIs($loan, 1);
                
                return [
                    'id' => $loan->id,
                    'principal_amount' => (float) $loan->principal_amount,
                    'interest_rate' => (float) $loan->interest_rate,
                    'tenure_months' => (int) $loan->tenure_months,
                    'emi_amount' => (float) $loan->emi_amount,
                    'total_repayment' => (float) $loan->total_repayment,
                    'status' => $loan->status,
                    'next_due_date' => optional($loan->next_due_date)->format('Y-m-d'),
                    'penalty_amount' => (float) $loan->penalty_amount,
                    'custom_penalty_amount' => $loan->custom_penalty_amount ? (float) $loan->custom_penalty_amount : null,
                    'upcoming_emis' => $upcomingEMIs,
                    'installments' => $loan->installments->map(function (LoanInstallment $installment) {
                        return [
                            'id' => $installment->id,
                            'due_date' => $installment->due_date->format('Y-m-d'),
                            'amount' => (float) $installment->amount,
                            'status' => $installment->status,
                            'penalty_amount' => (float) $installment->penalty_amount,
                            'paid_at' => optional($installment->paid_at)->toDateTimeString(),
                            'pay_url' => route('loans.installments.pay', $installment),
                        ];
                    })->toArray(),
                ];
            })
            ->toArray();
    }

    public function listInstallments(User $user, int $loanId)
    {
        $loan = $user->loans()->with('installments')->findOrFail($loanId);

        return $loan->installments->map(function (LoanInstallment $installment) {
            return [
                'id' => $installment->id,
                'due_date' => $installment->due_date->format('Y-m-d'),
                'amount' => (float) $installment->amount,
                'status' => $installment->status,
                'penalty_amount' => (float) $installment->penalty_amount,
                'paid_at' => optional($installment->paid_at)->toDateTimeString(),
                'pay_url' => route('loans.installments.pay', $installment),
            ];
        })->values();
    }

    public function markInstallmentPaid(LoanInstallment $installment, ?float $customPenalty = null, ?float $paymentAmount = null): void
    {
        $penalty = $customPenalty !== null ? $customPenalty : $installment->penalty_amount;

        if ($installment->due_date->isPast() && $customPenalty === null) {
            $penalty = config('loan.default_penalty', 593);
        }

        // If payment amount is provided and different from installment amount, adjust accordingly
        // Note: The payment_amount is for tracking purposes, the installment amount remains the same
        $installment->update([
            'status' => 'paid',
            'paid_at' => Carbon::now(),
            'penalty_amount' => $penalty,
        ]);

        $loan = $installment->loan;

        $nextInstallment = $loan->installments()
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->first();

        $loan->update([
            'status' => $nextInstallment ? 'active' : 'completed',
            'next_due_date' => $nextInstallment?->due_date,
            'custom_penalty_amount' => $customPenalty ?? $loan->custom_penalty_amount,
        ]);
    }

    public function loanReminders(User $user): array
    {
        return $user->loans()
            ->with(['installments' => function ($query) {
                $query->whereIn('status', ['pending', 'overdue'])
                    ->orderBy('due_date');
            }])
            ->get()
            ->flatMap(function (Loan $loan) {
                return $loan->installments->map(function (LoanInstallment $installment) use ($loan) {
                    $status = $installment->status;

                    if ($status === 'pending' && $installment->due_date->isPast()) {
                        $installment->update(['status' => 'overdue']);
                        $status = 'overdue';
                    }

                    return [
                        'loan_id' => $loan->id,
                        'installment_id' => $installment->id,
                        'due_date' => $installment->due_date->format('Y-m-d'),
                        'amount' => $installment->amount,
                        'penalty_amount' => $installment->penalty_amount,
                        'status' => $status,
                    ];
                });
            })
            ->values()
            ->toArray();
    }

    public function summarizeLoans(User $user): array
    {
        $loans = $user->loans()->with('installments')->get();

        $totalPrincipal = $loans->sum('principal_amount');
        $totalRepayment = $loans->sum('total_repayment');
        $pendingInstallments = $loans->flatMap->installments->where('status', 'pending');
        $pendingAmount = $pendingInstallments->sum('amount');

        return [
            'total_principal' => $totalPrincipal,
            'total_repayment' => $totalRepayment,
            'pending_amount' => $pendingAmount,
            'pending_installments' => $pendingInstallments->count(),
        ];
    }

    public function deleteInstallment(LoanInstallment $installment): void
    {
        $loan = $installment->loan;
        
        // Delete the installment
        $installment->delete();

        // Recalculate remaining balance and update loan
        $remainingInstallments = $loan->installments()->orderBy('due_date')->get();
        
        if ($remainingInstallments->isEmpty()) {
            // No installments left, mark loan as completed
            $loan->update([
                'status' => 'completed',
                'next_due_date' => null,
            ]);
        } else {
            // Update next due date to the earliest pending installment
            $nextInstallment = $remainingInstallments
                ->where('status', 'pending')
                ->first();
            
            $loan->update([
                'status' => $nextInstallment ? 'active' : 'completed',
                'next_due_date' => $nextInstallment?->due_date,
            ]);
        }
    }
    
    /**
     * Get upcoming EMIs with bouncing charge logic
     * Shows only 1 upcoming EMI with dates: 10th, 12th (if 10th bounces), 15th (if 12th bounces)
     * Penalty: ₹593 on 10th, ₹593 on 12th, custom on 15th
     */
    public function getUpcomingEMIs(Loan $loan, int $limit = 1): array
    {
        $pendingInstallments = $loan->installments()
            ->where('status', 'pending')
            ->orderBy('due_date')
            ->limit($limit)
            ->get();
        
        $upcomingEMIs = [];
        $bouncingCharge = 593; // Updated to 593
        
        foreach ($pendingInstallments as $installment) {
            $dueDate = Carbon::parse($installment->due_date);
            $month = $dueDate->format('F Y');
            
            // Always set to 10th of the month
            $baseDate = $dueDate->copy()->day(10);
            
            // Calculate which date to show based on current date
            $today = Carbon::now();
            $day10 = $baseDate->copy();
            $day12 = $baseDate->copy()->day(12);
            $day15 = $baseDate->copy()->day(15);
            
            $displayDate = $day10;
            $bouncingNote = '';
            $totalPenalty = 0;
            $totalAmount = (float) $installment->amount;
            
            // Calculate penalties based on bouncing
            if ($today->greaterThan($day10)) {
                $totalPenalty += $bouncingCharge; // First penalty on 10th
                $totalAmount += $bouncingCharge;
                $displayDate = $day12;
                $bouncingNote = 'Payment bounced on 10th. Penalty ₹593 applied. New due date: 12th';
                
                // If 12th has also passed, show 15th
                if ($today->greaterThan($day12)) {
                    $totalPenalty += $bouncingCharge; // Second penalty on 12th
                    $totalAmount += $bouncingCharge;
                    $displayDate = $day15;
                    $bouncingNote = 'Payment bounced on 10th & 12th. Penalty ₹1186 applied. Final due date: 15th';
                    
                    // If 15th has also passed, admin can add custom penalty
                    if ($today->greaterThan($day15)) {
                        $customPenalty = $installment->penalty_amount > 1186 ? $installment->penalty_amount - 1186 : 0;
                        if ($customPenalty > 0) {
                            $totalPenalty += $customPenalty;
                            $totalAmount += $customPenalty;
                            $bouncingNote = 'Payment bounced on 10th, 12th & 15th. Total penalty ₹' . number_format($totalPenalty, 2) . '. Admin custom penalty applied.';
                        } else {
                            $bouncingNote = 'Payment bounced on 10th, 12th & 15th. Admin can add custom penalty.';
                        }
                    }
                }
            } else {
                // Not yet bounced, but penalty will be applied on 10th
                $totalPenalty = $bouncingCharge;
                $totalAmount += $bouncingCharge;
            }
            
            $upcomingEMIs[] = [
                'id' => $installment->id,
                'installment_number' => $installment->id,
                'due_date' => $baseDate->format('Y-m-d'),
                'display_date' => $displayDate->format('Y-m-d'),
                'display_date_formatted' => $displayDate->format('d M Y'),
                'month' => $month,
                'amount' => (float) $installment->amount,
                'penalty_amount' => $totalPenalty,
                'total_amount' => $totalAmount,
                'bouncing_charge' => $bouncingCharge,
                'bouncing_note' => $bouncingNote,
                'status' => $installment->status,
                'pay_url' => route('loans.installments.pay', $installment),
            ];
        }
        
        return $upcomingEMIs;
    }
}

