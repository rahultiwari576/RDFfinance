# Dummy Data Summary

## Database Population Complete! ✅

The database has been successfully populated with comprehensive dummy data for testing the RDFFinance application.

## Data Overview

### Users (6 total)
- **1 Admin User**
  - Email: `rahulktiwari12@gmail.com`
  - Password: `admin123`
  - Role: Admin
  - Full profile with address, profession, education details

- **5 Regular Users** (all with password: `password123`)
  1. **Rajesh Kumar** - `rajesh.kumar@example.com`
     - Software Engineer, Gurgaon
     - Phone: +919876543211
     - Has 2 active loans

  2. **Priya Sharma** - `priya.sharma@example.com`
     - Marketing Manager, Bangalore
     - Phone: +919876543212
     - Has 1 active loan

  3. **Amit Patel** - `amit.patel@example.com`
     - Business Owner, Mumbai
     - Phone: +919876543213
     - Has 1 completed loan

  4. **Sneha Reddy** - `sneha.reddy@example.com`
     - Data Analyst, Hyderabad
     - Phone: +919876543214
     - Has 1 active loan with overdue installments

  5. **Vikram Singh** - `vikram.singh@example.com`
     - Financial Advisor, New Delhi
     - Phone: +919876543215
     - Has 1 active loan

### Loans (6 total)
1. **Loan #1** - Rajesh Kumar
   - Amount: ₹500,000
   - Interest: 12% | Tenure: 24 months
   - Status: Active
   - Mix of paid, pending, and overdue installments

2. **Loan #2** - Priya Sharma
   - Amount: ₹1,000,000
   - Interest: 10.5% | Tenure: 36 months
   - Status: Active
   - Mix of paid, pending, and overdue installments

3. **Loan #3** - Amit Patel
   - Amount: ₹200,000
   - Interest: 11% | Tenure: 12 months
   - Status: Completed (all installments paid)

4. **Loan #4** - Sneha Reddy
   - Amount: ₹750,000
   - Interest: 13.5% | Tenure: 30 months
   - Status: Active
   - Has overdue installments

5. **Loan #5** - Vikram Singh
   - Amount: ₹300,000
   - Interest: 9.75% | Tenure: 18 months
   - Status: Active

6. **Loan #6** - Rajesh Kumar (second loan)
   - Amount: ₹250,000
   - Interest: 11.5% | Tenure: 15 months
   - Status: Active

### Loan Installments (135 total)
- **Paid**: Multiple installments marked as paid
- **Pending**: Future installments scheduled
- **Overdue**: Past due installments with penalties

### OTPs (15 total)
- Each user has 3 OTP records:
  - 1 recent unverified OTP
  - 1 expired unverified OTP
  - 1 verified OTP

## How to Use

### Login Credentials

**Admin Access:**
- Email: `rahulktiwari12@gmail.com`
- Password: `admin123`

**Regular User Access:**
- Use any of the 5 user emails listed above
- Password: `password123` (same for all)

### Testing Scenarios

1. **Admin Dashboard**
   - Login as admin to see all users and loans
   - Manage users, loans, and customize EMI payments

2. **User Dashboard**
   - Login as any regular user
   - View personal loans and installments
   - See payment reminders and overdue notices

3. **Loan Management**
   - View active, completed, and defaulted loans
   - Track EMI payments and schedules
   - Handle overdue installments with penalties

4. **OTP System**
   - Test OTP-based Aadhar login
   - Verify OTP expiration and validation

## Re-seeding Data

If you need to reset and re-seed the database:

```bash
php artisan migrate:fresh --seed
```

Or run the seeder directly:

```bash
php artisan db:seed --class=DummyDataSeeder
```

## Notes

- All users have complete profile information (address, profession, education)
- Loans have realistic EMI calculations
- Installments have varied statuses (paid, pending, overdue)
- OTP records include different states (verified, unverified, expired)
- The data is designed to test all features of the finance application

---

**Project Status**: ✅ Ready for testing and development

