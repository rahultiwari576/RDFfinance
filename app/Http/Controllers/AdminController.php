<?php

namespace App\Http\Controllers;

use App\Mail\LoanApplicationMail;
use App\Models\Loan;
use App\Models\LoanDraft;
use App\Models\Otp;
use App\Models\User;
use App\Services\LoanService;
use App\Services\OtpService;
use Carbon\Carbon;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;

class AdminController extends Controller
{
    public function __construct(
        private readonly LoanService $loanService,
        private readonly OtpService $otpService
    ) {
        $this->middleware(['auth', 'admin']);
    }

    public function index()
    {
        $users = User::withCount('loans')->latest()->get();
        $loans = Loan::with(['user', 'installments'])->latest()->get();

        return view('admin.dashboard', compact('users', 'loans'));
    }

    public function users(): JsonResponse
    {
        $users = User::withCount('loans')->latest()->get();

        return response()->json([
            'status' => true,
            'users' => $users,
        ]);
    }

    public function getUser($userId): JsonResponse
    {
        $user = User::withCount('loans')->findOrFail($userId);

        return response()->json([
            'status' => true,
            'user' => $user,
        ]);
    }

    public function createUser(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'email', 'max:255', 'unique:users,email'],
            'password' => ['required', 'string', 'min:6'],
            'role' => ['required', 'in:admin,user'],
            'aadhar_number' => ['required', 'digits:12', 'unique:users,aadhar_number'],
            'pan_number' => ['required', 'string', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/'],
            'phone_number' => ['required', 'string', 'regex:/^[0-9]{10}$/'],
            'age' => ['required', 'integer', 'min:18', 'max:120'],
            'address_type' => ['required', 'string', 'in:RESIDENTIAL,PERMANENT,OFFICE'],
            'flat_building' => ['required', 'string', 'max:255'],
            'locality' => ['required', 'string', 'max:255'],
            'city' => ['required', 'string', 'max:255'],
            'state' => ['required', 'string', 'max:255'],
            'pincode' => ['required', 'string', 'digits:6'],
            'profession' => ['sometimes', 'nullable', 'string', 'max:255'],
            'education' => ['sometimes', 'nullable', 'string', 'max:255'],
            'additional_info' => ['sometimes', 'nullable', 'string'],
        ]);

        $user = User::create([
            'name' => $validated['name'],
            'email' => $validated['email'],
            'password' => Hash::make($validated['password']),
            'role' => $validated['role'],
            'aadhar_number' => $validated['aadhar_number'],
            'pan_number' => $validated['pan_number'],
            'phone_number' => $validated['phone_number'],
            'age' => $validated['age'],
            'address_type' => $validated['address_type'],
            'flat_building' => $validated['flat_building'],
            'locality' => $validated['locality'],
            'city' => $validated['city'],
            'state' => $validated['state'],
            'pincode' => $validated['pincode'],
            'profession' => $validated['profession'] ?? null,
            'education' => $validated['education'] ?? null,
            'additional_info' => $validated['additional_info'] ?? null,
            // maintain old columns too for safety if they exist
            'address' => $validated['flat_building'],
            'area' => $validated['locality'],
            'zip_code' => $validated['pincode'],
        ]);

        return response()->json([
            'status' => true,
            'message' => 'User created successfully.',
            'user' => $user,
        ], 201);
    }

    public function updateUser(Request $request, $userId): JsonResponse
    {
        $user = User::findOrFail($userId);

        $validated = $request->validate([
            'name' => ['sometimes', 'string', 'max:255'],
            'email' => ['sometimes', 'email', 'max:255', 'unique:users,email,' . $userId],
            'password' => ['sometimes', 'nullable', 'string', 'min:6'],
            'role' => ['sometimes', 'in:admin,user'],
            'aadhar_number' => ['sometimes', 'digits:12', 'unique:users,aadhar_number,' . $userId],
            'pan_number' => ['sometimes', 'string', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/'],
            'phone_number' => ['sometimes', 'string', 'regex:/^[0-9]{10}$/'],
            'age' => ['sometimes', 'integer', 'min:18', 'max:120'],
            'address_type' => ['sometimes', 'string', 'in:RESIDENTIAL,PERMANENT,OFFICE'],
            'flat_building' => ['sometimes', 'string', 'max:255'],
            'locality' => ['sometimes', 'string', 'max:255'],
            'city' => ['sometimes', 'string', 'max:255'],
            'state' => ['sometimes', 'string', 'max:255'],
            'pincode' => ['sometimes', 'string', 'digits:6'],
            'profession' => ['sometimes', 'nullable', 'string', 'max:255'],
            'education' => ['sometimes', 'nullable', 'string', 'max:255'],
            'additional_info' => ['sometimes', 'nullable', 'string'],
        ]);

        if (isset($validated['password']) && !empty($validated['password'])) {
            $validated['password'] = Hash::make($validated['password']);
        } else {
            unset($validated['password']);
        }

        if (isset($validated['flat_building'])) $validated['address'] = $validated['flat_building'];
        if (isset($validated['locality'])) $validated['area'] = $validated['locality'];
        if (isset($validated['pincode'])) $validated['zip_code'] = $validated['pincode'];

        $user->update($validated);

        return response()->json([
            'status' => true,
            'message' => 'User updated successfully.',
            'user' => $user->fresh(),
        ]);
    }

    public function deleteUser($userId): JsonResponse
    {
        $user = User::findOrFail($userId);

        // Prevent admin from deleting themselves
        if ($user->id === auth()->id()) {
            return response()->json([
                'status' => false,
                'message' => 'You cannot delete your own account.',
            ], 403);
        }

        $user->delete();

        return response()->json([
            'status' => true,
            'message' => 'User deleted successfully.',
        ]);
    }

    public function allLoans(): JsonResponse
    {
        $loans = Loan::with(['user', 'installments'])->latest()->get();

        return response()->json([
            'status' => true,
            'loans' => $loans,
        ]);
    }

    public function deleteLoan($loanId): JsonResponse
    {
        $loan = Loan::findOrFail($loanId);
        $loan->delete();

        return response()->json([
            'status' => true,
            'message' => 'Loan deleted successfully.',
        ]);
    }

    public function getUserLoans($userId): JsonResponse
    {
        $user = User::findOrFail($userId);
        $loans = $this->loanService->listLoans($user);

        return response()->json([
            'status' => true,
            'loans' => $loans,
        ]);
    }

    public function getUserReminders($userId): JsonResponse
    {
        $user = User::findOrFail($userId);
        $reminders = $this->loanService->loanReminders($user);

        return response()->json([
            'status' => true,
            'reminders' => $reminders,
        ]);
    }

    public function getUserLoanSummary($userId): JsonResponse
    {
        $user = User::findOrFail($userId);
        $summary = $this->loanService->summarizeLoans($user);

        return response()->json([
            'status' => true,
            'summary' => $summary,
        ]);
    }

    public function customizeEmiPayment(Request $request, $installmentId = null): JsonResponse
    {
        $validated = $request->validate([
            'custom_penalty_amount' => ['required', 'numeric', 'min:0'],
            'mark_as_paid' => ['sometimes', 'boolean'],
            'loan_id' => ['sometimes', 'integer', 'exists:loans,id'],
            'due_date' => ['sometimes', 'date'],
        ]);

        // Find installment by ID or by loan_id and due_date
        if ($installmentId) {
            $installment = \App\Models\LoanInstallment::findOrFail($installmentId);
        } elseif ($validated['loan_id'] && $validated['due_date']) {
            $installment = \App\Models\LoanInstallment::where('loan_id', $validated['loan_id'])
                ->whereDate('due_date', $validated['due_date'])
                ->firstOrFail();
        } else {
            return response()->json([
                'status' => false,
                'message' => 'Installment ID or Loan ID with Due Date is required.',
            ], 400);
        }

        if ($request->boolean('mark_as_paid')) {
            $this->loanService->markInstallmentPaid($installment, $validated['custom_penalty_amount']);
            
            return response()->json([
                'status' => true,
                'message' => 'EMI marked as paid with custom penalty.',
            ]);
        } else {
            // Just update penalty without marking as paid
            $installment->update([
                'penalty_amount' => $validated['custom_penalty_amount'],
            ]);

            return response()->json([
                'status' => true,
                'message' => 'Penalty amount updated successfully.',
            ]);
        }
    }

    public function applyLoan(Request $request): JsonResponse
    {
        // This is a comprehensive loan application handler
        // Validate all required fields
        $validated = $request->validate([
            // Customer details
            'customer_type' => ['required', 'in:new,existing'],
            'existing_user_id' => ['required_if:customer_type,existing', 'nullable', 'exists:users,id'],
            'customer_first_name' => ['required', 'string', 'max:255'],
            'customer_last_name' => ['required', 'string', 'max:255'],
            'customer_password' => ['required_if:customer_type,new', 'nullable', 'string', 'min:6'],
            'customer_aadhar_number' => ['required', 'digits:12', 'unique:users,aadhar_number,' . $request->input('existing_user_id')],
            'customer_mobile_number' => ['required', 'regex:/^[0-9]{10}$/'],
            'customer_alternative_mobile' => ['nullable', 'regex:/^[0-9]{10}$/'],
            'customer_email' => ['required', 'email', 'unique:users,email,' . $request->input('existing_user_id')],
            'customer_pan_number' => ['required', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/'],
            'customer_address_type' => ['required', 'in:RESIDENTIAL,PERMANENT,OFFICE'],
            'customer_flat_building' => ['required', 'string', 'max:255'],
            'customer_locality' => ['required', 'string', 'max:255'],
            'customer_city' => ['required', 'string', 'max:255'],
            'customer_state' => ['required', 'string', 'max:255'],
            'customer_pincode' => ['required', 'digits:6'],
            'customer_employment_type' => ['required', 'in:self_employed,salaried'],
            
            // Guarantor
            'guarantor_first_name' => ['required', 'string', 'max:255'],
            'guarantor_last_name' => ['required', 'string', 'max:255'],
            'guarantor_aadhar' => ['required', 'digits:12'],
            'guarantor_pan' => ['required', 'regex:/^[A-Z]{5}[0-9]{4}[A-Z]{1}$/'],
            'guarantor_mobile' => ['required', 'regex:/^[0-9]{10}$/'],
            
            // Vehicle
            'vehicle_type' => ['required', 'in:new,used'],
            'vehicle_company_name' => ['required_if:vehicle_type,new', 'nullable', 'string'],
            'vehicle_model_name' => ['required_if:vehicle_type,new', 'nullable', 'string'],
            'used_vehicle_company' => ['required_if:vehicle_type,used', 'nullable', 'string'],
            'used_vehicle_model' => ['required_if:vehicle_type,used', 'nullable', 'string'],
            'engine_number' => ['required_if:vehicle_type,used', 'nullable', 'string'],
            'chassis_number' => ['required_if:vehicle_type,used', 'nullable', 'string'],
            'registration_number' => ['required_if:vehicle_type,used', 'nullable', 'string'],
            'registration_date' => ['required_if:vehicle_type,used', 'nullable', 'date'],
            'registration_validity' => ['required_if:vehicle_type,used', 'nullable', 'date'],
            'owner_name' => ['required_if:vehicle_type,used', 'nullable', 'string'],
            'vehicle_color' => ['required_if:vehicle_type,used', 'nullable', 'string'],
            'rc_other_details' => ['nullable', 'string'],
            
            // Additional
            'mobile_otp' => ['nullable', 'string'],
            'office_address' => ['nullable', 'string'],
            'reference1_name' => ['nullable', 'string'],
            'reference1_mobile' => ['nullable', 'regex:/^[0-9]{10}$/'],
            'reference1_address' => ['nullable', 'string'],
            'reference2_name' => ['nullable', 'string'],
            'reference2_mobile' => ['nullable', 'regex:/^[0-9]{10}$/'],
            'reference2_address' => ['nullable', 'string'],
            'cibil_score' => ['nullable', 'string'],
            'cibil_details' => ['nullable', 'string'],
            
            // Sanction & Bank
            'aadhar_otp' => ['nullable', 'string'],
            'principal_amount' => ['required', 'numeric', 'min:1000'],
            'interest_rate' => ['required', 'numeric', 'min:1'],
            'tenure_months' => ['required', 'integer', 'min:1'],
            'emi_count' => ['required', 'integer', 'min:1'],
            'time_period' => ['nullable', 'string'],
            'sanction_letter' => ['nullable', 'string'],
            'bank_account_number' => ['required', 'string'],
            'bank_account_name' => ['required', 'string'],
            'bank_ifsc' => ['required', 'string'],
            'bank_upi' => ['nullable', 'string'],
            
            // Documents
            'tax_invoice' => ['required_if:vehicle_type,new', 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'rc_document' => ['required_if:vehicle_type,used', 'nullable', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'insurance' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'delivery_photo' => ['required', 'file', 'mimes:jpg,jpeg,png', 'max:5120'],
            'aadhar_card' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'pan_card' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'address_proof' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'rto_booklet' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'cheque_1' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'cheque_2' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'cheque_3' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'guarantor_aadhar_doc' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'guarantor_pan_doc' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
            'guarantor_cheque' => ['required', 'file', 'mimes:pdf,jpg,jpeg,png', 'max:5120'],
        ]);

        try {
            \DB::beginTransaction();

            // Create or get user
            if ($validated['customer_type'] === 'new') {
                $user = User::create([
                    'name' => $validated['customer_first_name'] . ' ' . $validated['customer_last_name'],
                    'first_name' => $validated['customer_first_name'],
                    'last_name' => $validated['customer_last_name'],
                    'email' => $validated['customer_email'],
                    'password' => Hash::make($validated['customer_password']),
                    'role' => 'user',
                    'aadhar_number' => $validated['customer_aadhar_number'],
                    'pan_number' => $validated['customer_pan_number'],
                    'phone_number' => $validated['customer_mobile_number'],
                    'alternative_phone_number' => $validated['customer_alternative_mobile'] ?? null,
                    'address_type' => $validated['customer_address_type'],
                    'address' => $validated['customer_flat_building'],
                    'area' => $validated['customer_locality'],
                    'city' => $validated['customer_city'],
                    'state' => $validated['customer_state'],
                    'zip_code' => $validated['customer_pincode'],
                    'employment_type' => $validated['customer_employment_type'],
                    'age' => 25, // Default, can be updated later
                ]);
            } else {
                // Get existing user - admin can apply loan for any user
                $user = User::findOrFail($validated['existing_user_id']);
                
                // Ensure user is not an admin (optional check)
                if ($user->isAdmin()) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Cannot apply loan for admin users.',
                    ], 400);
                }

                // Update existing user with potentially changed data
                $user->update([
                    'name' => $validated['customer_first_name'] . ' ' . $validated['customer_last_name'],
                    'first_name' => $validated['customer_first_name'],
                    'last_name' => $validated['customer_last_name'],
                    'email' => $validated['customer_email'],
                    'aadhar_number' => $validated['customer_aadhar_number'],
                    'pan_number' => $validated['customer_pan_number'],
                    'phone_number' => $validated['customer_mobile_number'],
                    'alternative_phone_number' => $validated['customer_alternative_mobile'] ?? null,
                    'address_type' => $validated['customer_address_type'],
                    'address' => $validated['customer_flat_building'],
                    'area' => $validated['customer_locality'],
                    'city' => $validated['customer_city'],
                    'state' => $validated['customer_state'],
                    'zip_code' => $validated['customer_pincode'],
                    'employment_type' => $validated['customer_employment_type'],
                ]);
            }

            // Verify Mobile OTP (Bypassed for now)
            /*
            $mobileNumber = $validated['customer_type'] === 'new' 
                ? $validated['customer_mobile_number'] 
                : $user->phone_number;

            $otpVerified = Otp::where('mobile', $mobileNumber)
                ->where('code', $validated['mobile_otp'])
                ->whereNotNull('verified_at')
                ->exists();

            if (!$otpVerified) {
                return response()->json([
                    'status' => false,
                    'message' => 'Mobile OTP verification failed. Please verify OTP in Step 2.',
                ], 400);
            }
            */

            // Calculate EMI
            $principal = $validated['principal_amount'];
            $rate = $validated['interest_rate'] / 100 / 12;
            $tenure = $validated['tenure_months'];
            $emi = ($principal * $rate * pow(1 + $rate, $tenure)) / (pow(1 + $rate, $tenure) - 1);
            $totalRepayment = $emi * $tenure;

            // Create loan
            $loan = Loan::create([
                'user_id' => $user->id,
                'principal_amount' => $principal,
                'interest_rate' => $validated['interest_rate'],
                'tenure_months' => $tenure,
                'emi_amount' => round($emi, 2),
                'total_repayment' => round($totalRepayment, 2),
                'status' => 'active',
                'vehicle_type' => $validated['vehicle_type'],
                'mobile_otp' => $validated['mobile_otp'],
                'customer_flat_building' => $validated['customer_flat_building'] ?? null,
                'customer_locality' => $validated['customer_locality'] ?? null,
                'customer_city' => $validated['customer_city'] ?? null,
                'customer_state' => $validated['customer_state'] ?? null,
                'customer_pincode' => $validated['customer_pincode'] ?? null,
                'office_address' => $validated['office_address'] ?? null,
                'aadhar_otp' => $validated['aadhar_otp'],
                'cibil_score' => $validated['cibil_score'] ?? null,
                'cibil_details' => $validated['cibil_details'] ?? null,
                'sanction_letter' => $validated['sanction_letter'] ?? null,
                'penalty_amount' => 590,
                'max_penalty_applications' => 3,
                'penalty_dates' => [10, 12, 15],
            ]);

            // Create guarantor
            \App\Models\Guarantor::create([
                'loan_id' => $loan->id,
                'first_name' => $validated['guarantor_first_name'],
                'last_name' => $validated['guarantor_last_name'],
                'aadhar_number' => $validated['guarantor_aadhar'],
                'pan_number' => $validated['guarantor_pan'],
                'mobile_number' => $validated['guarantor_mobile'],
            ]);

            // Create vehicle details
            $vehicleData = [
                'loan_id' => $loan->id,
                'vehicle_type' => $validated['vehicle_type'],
            ];
            
            if ($validated['vehicle_type'] === 'new') {
                $vehicleData['company_name'] = $validated['vehicle_company_name'];
                $vehicleData['model_name'] = $validated['vehicle_model_name'];
            } else {
                $vehicleData['company_name'] = $validated['used_vehicle_company'];
                $vehicleData['model_name'] = $validated['used_vehicle_model'];
                $vehicleData['engine_number'] = $validated['engine_number'];
                $vehicleData['chassis_number'] = $validated['chassis_number'];
                $vehicleData['registration_number'] = $validated['registration_number'];
                $vehicleData['registration_date'] = $validated['registration_date'];
                $vehicleData['registration_validity'] = $validated['registration_validity'];
                $vehicleData['owner_name'] = $validated['owner_name'];
                $vehicleData['vehicle_color'] = $validated['vehicle_color'];
                $vehicleData['rc_other_details'] = $validated['rc_other_details'] ?? null;
            }
            
            \App\Models\VehicleDetail::create($vehicleData);

            // Create references (optional)
            if (!empty($validated['reference1_name']) || !empty($validated['reference1_mobile']) || !empty($validated['reference1_address'])) {
                \App\Models\Reference::create([
                    'loan_id' => $loan->id,
                    'name' => $validated['reference1_name'] ?? '',
                    'mobile_number' => $validated['reference1_mobile'] ?? '',
                    'address' => $validated['reference1_address'] ?? '',
                    'reference_number' => 1,
                ]);
            }
            
            if (!empty($validated['reference2_name']) || !empty($validated['reference2_mobile']) || !empty($validated['reference2_address'])) {
                \App\Models\Reference::create([
                    'loan_id' => $loan->id,
                    'name' => $validated['reference2_name'] ?? '',
                    'mobile_number' => $validated['reference2_mobile'] ?? '',
                    'address' => $validated['reference2_address'] ?? '',
                    'reference_number' => 2,
                ]);
            }

            // Create bank details
            \App\Models\BankDetail::create([
                'loan_id' => $loan->id,
                'account_number' => $validated['bank_account_number'],
                'account_holder_name' => $validated['bank_account_name'],
                'ifsc_code' => $validated['bank_ifsc'],
                'upi_id' => $validated['bank_upi'] ?? null,
            ]);

            // Upload documents
            $documentTypes = [
                'tax_invoice' => 'tax_invoice',
                'rc_document' => 'rc_document',
                'insurance' => 'insurance',
                'delivery_photo' => 'delivery_photo',
                'aadhar_card' => 'aadhar_card',
                'pan_card' => 'pan_card',
                'address_proof' => 'address_proof',
                'rto_booklet' => 'rto_booklet',
                'cheque_1' => 'cheque_1',
                'cheque_2' => 'cheque_2',
                'cheque_3' => 'cheque_3',
                'guarantor_aadhar_doc' => 'guarantor_aadhar',
                'guarantor_pan_doc' => 'guarantor_pan',
                'guarantor_cheque' => 'guarantor_cheque',
            ];

            foreach ($documentTypes as $field => $docType) {
                if ($request->hasFile($field)) {
                    $file = $request->file($field);
                    $path = $file->store("loans/{$loan->id}/documents", 'public');
                    
                    \App\Models\LoanDocument::create([
                        'loan_id' => $loan->id,
                        'document_type' => $docType,
                        'file_path' => $path,
                        'original_filename' => $file->getClientOriginalName(),
                    ]);
                }
            }

            // Generate installments
            $calculation = $this->loanService->calculateEmi([
                'principal_amount' => $principal,
                'interest_rate' => $validated['interest_rate'],
                'tenure_months' => $tenure,
            ]);
            $this->loanService->createInstallments($loan, $calculation['schedule'], 590);
            
            // Update loan with next_due_date
            $firstInstallment = $loan->installments()->orderBy('due_date')->first();
            if ($firstInstallment) {
                $loan->update(['next_due_date' => $firstInstallment->due_date]);
            }

            \DB::commit();

            // Send email notification
            try {
                // Reload loan with all relationships
                $loan->load(['user', 'bankDetail', 'guarantor', 'installments']);
                Mail::to($user->email)->send(new LoanApplicationMail($loan));
            } catch (\Exception $e) {
                // Log email error but don't fail the request
                \Log::error('Failed to send loan application email: ' . $e->getMessage());
            }

            return response()->json([
                'status' => true,
                'message' => 'Loan application submitted successfully.',
                'loan' => $loan,
            ], 201);

        } catch (\Exception $e) {
            \DB::rollBack();
            return response()->json([
                'status' => false,
                'message' => 'Failed to submit loan application: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function saveLoanDraft(Request $request): JsonResponse
    {
        try {
            $formData = $request->except(['_token', 'files']);
            $currentStep = $request->input('current_step', 1);
            $userId = $request->input('existing_user_id') ?? $request->input('user_id');
            
            // Get or create draft
            $draft = LoanDraft::updateOrCreate(
                [
                    'admin_id' => Auth::id(),
                    'user_id' => $userId,
                ],
                [
                    'existing_user_id' => $request->input('existing_user_id'),
                    'form_data' => $formData,
                    'current_step' => $currentStep,
                ]
            );

            return response()->json([
                'status' => true,
                'message' => 'Draft saved successfully.',
                'draft_id' => $draft->id,
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to save draft: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function loadLoanDraft(Request $request): JsonResponse
    {
        try {
            $userId = $request->input('user_id');
            
            $draft = LoanDraft::where('admin_id', Auth::id())
                ->where(function($query) use ($userId) {
                    $query->where('user_id', $userId)
                          ->orWhere('existing_user_id', $userId);
                })
                ->latest()
                ->first();

            if (!$draft) {
                return response()->json([
                    'status' => false,
                    'message' => 'No draft found.',
                ]);
            }

            return response()->json([
                'status' => true,
                'draft' => [
                    'form_data' => $draft->form_data,
                    'current_step' => $draft->current_step,
                    'saved_at' => $draft->updated_at->format('Y-m-d H:i:s'),
                ],
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to load draft: ' . $e->getMessage(),
            ], 500);
        }
    }

    public function deleteLoanDraft(Request $request): JsonResponse
    {
        try {
            $userId = $request->input('user_id');
            
            LoanDraft::where('admin_id', Auth::id())
                ->where(function($query) use ($userId) {
                    $query->where('user_id', $userId)
                          ->orWhere('existing_user_id', $userId);
                })
                ->delete();

            return response()->json([
                'status' => true,
                'message' => 'Draft deleted successfully.',
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Failed to delete draft: ' . $e->getMessage(),
            ], 500);
        }
    }
    public function sendLoanOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mobile' => ['required', 'regex:/^[0-9]{10}$/'],
        ]);

        $mobile = $validated['mobile'];
        $this->otpService->generateOtpForMobile($mobile);

        // In a real app, send SMS here. For now, we just return success.
        
        return response()->json([
            'status' => true,
            'message' => "OTP sent to mobile number {$mobile}",
        ]);
    }

    public function verifyLoanOtp(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'mobile' => ['required', 'regex:/^[0-9]{10}$/'],
            'otp' => ['required', 'string', 'size:6'],
        ]);

        $verification = $this->otpService->verifyMobileOtp($validated['mobile'], $validated['otp']);

        if (!$verification['status']) {
            return response()->json($verification, 400);
        }

        return response()->json([
            'status' => true,
            'message' => 'OTP verified successfully.',
        ]);
    }
}
