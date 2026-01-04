<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Loan;
use App\Models\LoanInstallment;
use App\Models\Otp;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DummyDataSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Clear existing data (optional - comment out if you want to keep existing data)
        // LoanInstallment::truncate();
        // Loan::truncate();
        // Otp::truncate();
        // User::where('role', '!=', 'admin')->delete();

        // Create Admin User
        $admin = User::firstOrCreate(
            ['email' => 'rahulktiwari12@gmail.com'],
            [
                'name' => 'Admin User',
                'first_name' => 'Admin',
                'last_name' => 'User',
                'password' => Hash::make('admin123'),
                'role' => 'admin',
                'aadhar_number' => '123456789012',
                'pan_number' => 'ABCDE1234F',
                'phone_number' => '+919876543210',
                'age' => 35,
                'flat_building' => '123 Admin Building',
                'locality' => 'Business District',
                'city' => 'Mumbai',
                'pincode' => '400001',
                'state' => 'Maharashtra',
                'address' => '123 Admin Street',
                'area' => 'Business District',
                'zip_code' => '400001',
                'profession' => 'Administrator',
                'education' => 'MBA',
                'address_type' => 'RESIDENTIAL',
                'employment_type' => 'salaried',
            ]
        );

        // Create Regular Users
        $users = [
            [
                'name' => 'Rajesh Kumar',
                'first_name' => 'Rajesh',
                'last_name' => 'Kumar',
                'email' => 'rajesh.kumar@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'aadhar_number' => '234567890123',
                'pan_number' => 'BCDEF2345G',
                'phone_number' => '+919876543211',
                'alternative_phone_number' => '+919876543212',
                'age' => 28,
                'flat_building' => '456 Building, Floor 2',
                'locality' => 'Sector 5',
                'city' => 'Gurgaon',
                'state' => 'Haryana',
                'pincode' => '122001',
                'address' => '456 Main Road, Sector 5',
                'area' => 'Gurgaon',
                'zip_code' => '122001',
                'profession' => 'Software Engineer',
                'education' => 'B.Tech Computer Science',
                'additional_info' => 'Working in IT sector for 5 years',
                'address_type' => 'RESIDENTIAL',
                'employment_type' => 'salaried',
            ],
            [
                'name' => 'Priya Sharma',
                'first_name' => 'Priya',
                'last_name' => 'Sharma',
                'email' => 'priya.sharma@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'aadhar_number' => '345678901234',
                'pan_number' => 'CDEFG3456H',
                'phone_number' => '+919876543212',
                'alternative_phone_number' => '+919876543213',
                'age' => 32,
                'address' => '789 Park Avenue',
                'area' => 'Koramangala',
                'city' => 'Bangalore',
                'state' => 'Karnataka',
                'zip_code' => '560095',
                'profession' => 'Marketing Manager',
                'education' => 'MBA Marketing',
                'additional_info' => 'Experienced in digital marketing',
                'address_type' => 'PERMANENT',
                'employment_type' => 'salaried',
            ],
            [
                'name' => 'Amit Patel',
                'first_name' => 'Amit',
                'last_name' => 'Patel',
                'email' => 'amit.patel@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'aadhar_number' => '456789012345',
                'pan_number' => 'DEFGH4567I',
                'phone_number' => '+919876543213',
                'age' => 45,
                'address' => '321 Business Park',
                'area' => 'Andheri East',
                'city' => 'Mumbai',
                'state' => 'Maharashtra',
                'zip_code' => '400069',
                'profession' => 'Business Owner',
                'education' => 'B.Com',
                'additional_info' => 'Running a small manufacturing unit',
                'address_type' => 'OFFICE',
                'employment_type' => 'self_employed',
            ],
            [
                'name' => 'Sneha Reddy',
                'first_name' => 'Sneha',
                'last_name' => 'Reddy',
                'email' => 'sneha.reddy@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'aadhar_number' => '567890123456',
                'pan_number' => 'EFGHI5678J',
                'phone_number' => '+919876543214',
                'age' => 26,
                'address' => '654 Tech Park Road',
                'area' => 'Hitech City',
                'city' => 'Hyderabad',
                'state' => 'Telangana',
                'zip_code' => '500081',
                'profession' => 'Data Analyst',
                'education' => 'M.Sc Statistics',
                'additional_info' => 'Specialized in data science',
                'address_type' => 'RESIDENTIAL',
                'employment_type' => 'salaried',
            ],
            [
                'name' => 'Vikram Singh',
                'first_name' => 'Vikram',
                'last_name' => 'Singh',
                'email' => 'vikram.singh@example.com',
                'password' => Hash::make('password123'),
                'role' => 'user',
                'aadhar_number' => '678901234567',
                'pan_number' => 'FGHIJ6789K',
                'phone_number' => '+919876543215',
                'age' => 38,
                'address' => '987 MG Road',
                'area' => 'Connaught Place',
                'city' => 'New Delhi',
                'state' => 'Delhi',
                'zip_code' => '110001',
                'profession' => 'Financial Advisor',
                'education' => 'CA',
                'additional_info' => 'Certified Chartered Accountant',
                'address_type' => 'PERMANENT',
                'employment_type' => 'salaried',
            ],
        ];

        $createdUsers = [];
        foreach ($users as $userData) {
            $user = User::firstOrCreate(
                ['email' => $userData['email']],
                $userData
            );
            $createdUsers[] = $user;
        }

        // Create Loans for Users
        $loansData = [
            // Loan 1 - Active loan with pending installments
            [
                'user_id' => $createdUsers[0]->id,
                'principal_amount' => 500000.00,
                'interest_rate' => 12.00,
                'tenure_months' => 24,
                'emi_amount' => 23536.75,
                'total_repayment' => 564882.00,
                'status' => 'active',
                'next_due_date' => Carbon::now()->addDays(15),
                'penalty_amount' => 100.00,
            ],
            // Loan 2 - Active loan with some paid installments
            [
                'user_id' => $createdUsers[1]->id,
                'principal_amount' => 1000000.00,
                'interest_rate' => 10.50,
                'tenure_months' => 36,
                'emi_amount' => 32509.45,
                'total_repayment' => 1170340.20,
                'status' => 'active',
                'next_due_date' => Carbon::now()->addDays(8),
                'penalty_amount' => 150.00,
            ],
            // Loan 3 - Completed loan
            [
                'user_id' => $createdUsers[2]->id,
                'principal_amount' => 200000.00,
                'interest_rate' => 11.00,
                'tenure_months' => 12,
                'emi_amount' => 17661.16,
                'total_repayment' => 211933.92,
                'status' => 'completed',
                'next_due_date' => null,
                'penalty_amount' => 100.00,
            ],
            // Loan 4 - Active loan with overdue
            [
                'user_id' => $createdUsers[3]->id,
                'principal_amount' => 750000.00,
                'interest_rate' => 13.50,
                'tenure_months' => 30,
                'emi_amount' => 27089.23,
                'total_repayment' => 812676.90,
                'status' => 'active',
                'next_due_date' => Carbon::now()->subDays(5),
                'penalty_amount' => 200.00,
            ],
            // Loan 5 - Active loan
            [
                'user_id' => $createdUsers[4]->id,
                'principal_amount' => 300000.00,
                'interest_rate' => 9.75,
                'tenure_months' => 18,
                'emi_amount' => 17823.45,
                'total_repayment' => 320822.10,
                'status' => 'active',
                'next_due_date' => Carbon::now()->addDays(22),
                'penalty_amount' => 100.00,
            ],
            // Loan 6 - Another active loan for first user
            [
                'user_id' => $createdUsers[0]->id,
                'principal_amount' => 250000.00,
                'interest_rate' => 11.50,
                'tenure_months' => 15,
                'emi_amount' => 18345.67,
                'total_repayment' => 275185.05,
                'status' => 'active',
                'next_due_date' => Carbon::now()->addDays(30),
                'penalty_amount' => 100.00,
            ],
        ];

        $createdLoans = [];
        foreach ($loansData as $loanData) {
            $loan = Loan::create($loanData);
            $createdLoans[] = $loan;
        }

        // Create Installments for each loan
        foreach ($createdLoans as $loan) {
            // Calculate start date - loans started some months ago
            $monthsAgo = floor($loan->tenure_months * 0.4); // Loan started 40% of tenure ago
            $startDate = Carbon::now()->subMonths($monthsAgo)->startOfMonth();
            
            // For completed loans, mark all as paid
            $isCompleted = $loan->status === 'completed';
            
            // For active loans, create mix of paid, pending, and overdue
            $paidCount = 0;
            $paidThreshold = floor($loan->tenure_months * 0.3); // First 30% should be paid
            
            for ($i = 0; $i < $loan->tenure_months; $i++) {
                $dueDate = $startDate->copy()->addMonths($i + 1);
                $daysDiff = Carbon::now()->diffInDays($dueDate, false);
                
                // Determine status based on date and position
                if ($isCompleted) {
                    $status = 'paid';
                    $isPaid = true;
                } elseif ($paidCount < $paidThreshold && $daysDiff < 30) {
                    // First 30% of installments that are due are paid
                    $status = 'paid';
                    $isPaid = true;
                    $paidCount++;
                } elseif ($daysDiff < -5) {
                    // More than 5 days past due = overdue
                    $status = 'overdue';
                    $isPaid = false;
                } elseif ($daysDiff < 0) {
                    // Recently past due, mix of paid and overdue
                    $status = (rand(0, 100) < 40) ? 'paid' : 'overdue';
                    $isPaid = ($status === 'paid');
                    if ($isPaid) $paidCount++;
                } else {
                    // Future installments are pending
                    $status = 'pending';
                    $isPaid = false;
                }
                
                LoanInstallment::create([
                    'loan_id' => $loan->id,
                    'due_date' => $dueDate,
                    'amount' => $loan->emi_amount,
                    'status' => $status,
                    'paid_at' => $isPaid ? $dueDate->copy()->addDays(rand(0, 5)) : null,
                    'penalty_amount' => ($status === 'overdue') ? $loan->penalty_amount : 0,
                ]);
            }
        }

        // Create some OTP records
        foreach ($createdUsers as $user) {
            // Create a recent OTP (not expired)
            Otp::create([
                'user_id' => $user->id,
                'code' => str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT),
                'expires_at' => Carbon::now()->addMinutes(10),
                'verified_at' => null,
            ]);

            // Create an expired OTP
            Otp::create([
                'user_id' => $user->id,
                'code' => str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT),
                'expires_at' => Carbon::now()->subHours(2),
                'verified_at' => null,
            ]);

            // Create a verified OTP
            Otp::create([
                'user_id' => $user->id,
                'code' => str_pad(rand(0, 999999), 6, '0', STR_PAD_LEFT),
                'expires_at' => Carbon::now()->subHours(1),
                'verified_at' => Carbon::now()->subHours(1)->addMinutes(5),
            ]);
        }

        $this->command->info('Dummy data seeded successfully!');
        $this->command->info('Created:');
        $this->command->info('- 1 Admin user (rahulktiwari12@gmail.com / admin123)');
        $this->command->info('- ' . count($createdUsers) . ' Regular users (password: password123)');
        $this->command->info('- ' . count($createdLoans) . ' Loans');
        $this->command->info('- Multiple installments for each loan');
        $this->command->info('- OTP records for each user');
    }
}

