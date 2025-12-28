<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Application Submitted</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border: 1px solid #ddd;
        }
        .section {
            background: white;
            padding: 20px;
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .section-title {
            color: #667eea;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 15px;
            border-bottom: 2px solid #667eea;
            padding-bottom: 10px;
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
        }
        .detail-value {
            color: #333;
            text-align: right;
        }
        .highlight {
            background: #fff3cd;
            padding: 15px;
            border-left: 4px solid #ffc107;
            margin: 20px 0;
            border-radius: 4px;
        }
        .button {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            text-align: center;
        }
        .footer {
            background: #333;
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 0 0 10px 10px;
            font-size: 12px;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🎉 Loan Application Submitted Successfully!</h1>
        <p>Loan Application #{{ $loan->id }}</p>
    </div>

    <div class="content">
        <div class="section">
            <div class="section-title">👤 User Information</div>
            <div class="detail-row">
                <span class="detail-label">Name:</span>
                <span class="detail-value">{{ $user->name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Email:</span>
                <span class="detail-value">{{ $user->email }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Mobile Number:</span>
                <span class="detail-value">{{ $user->phone_number }}</span>
            </div>
            @if($user->alternative_phone_number)
            <div class="detail-row">
                <span class="detail-label">Alternative Mobile:</span>
                <span class="detail-value">{{ $user->alternative_phone_number }}</span>
            </div>
            @endif
            <div class="detail-row">
                <span class="detail-label">Aadhar Number:</span>
                <span class="detail-value">{{ $user->aadhar_number }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">PAN Number:</span>
                <span class="detail-value">{{ $user->pan_number }}</span>
            </div>
        </div>

        @if($bankDetail)
        <div class="section">
            <div class="section-title">🏦 Bank Details</div>
            <div class="detail-row">
                <span class="detail-label">Account Number:</span>
                <span class="detail-value">{{ $bankDetail->account_number }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Account Holder Name:</span>
                <span class="detail-value">{{ $bankDetail->account_holder_name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">IFSC Code:</span>
                <span class="detail-value">{{ $bankDetail->ifsc_code }}</span>
            </div>
            @if($bankDetail->upi_id)
            <div class="detail-row">
                <span class="detail-label">UPI ID:</span>
                <span class="detail-value">{{ $bankDetail->upi_id }}</span>
            </div>
            @endif
        </div>
        @endif

        @if($guarantor)
        <div class="section">
            <div class="section-title">🤝 Guarantor / Co-Applicant Details</div>
            <div class="detail-row">
                <span class="detail-label">Name:</span>
                <span class="detail-value">{{ $guarantor->first_name }} {{ $guarantor->last_name }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Aadhar Number:</span>
                <span class="detail-value">{{ $guarantor->aadhar_number }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">PAN Number:</span>
                <span class="detail-value">{{ $guarantor->pan_number }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Mobile Number:</span>
                <span class="detail-value">{{ $guarantor->mobile_number }}</span>
            </div>
        </div>
        @endif

        <div class="section">
            <div class="section-title">📋 Loan Details (Purpose)</div>
            <div class="detail-row">
                <span class="detail-label">Loan ID:</span>
                <span class="detail-value">#{{ $loan->id }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Principal Amount:</span>
                <span class="detail-value">₹{{ number_format($loan->principal_amount, 2) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Interest Rate:</span>
                <span class="detail-value">{{ number_format($loan->interest_rate, 2) }}%</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Tenure:</span>
                <span class="detail-value">{{ $loan->tenure_months }} months</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">EMI Amount:</span>
                <span class="detail-value">₹{{ number_format($loan->emi_amount, 2) }}</span>
            </div>
            <div class="detail-row">
                <span class="detail-label">Total Repayment:</span>
                <span class="detail-value">₹{{ number_format($loan->total_repayment, 2) }}</span>
            </div>
            @if($loan->vehicle_type)
            <div class="detail-row">
                <span class="detail-label">Vehicle Type:</span>
                <span class="detail-value">{{ ucfirst($loan->vehicle_type) }}</span>
            </div>
            @endif
        </div>

        @if($upcomingEMI)
        <div class="section">
            <div class="section-title">📅 Upcoming EMI</div>
            <div class="highlight">
                <div class="detail-row">
                    <span class="detail-label">Due Date:</span>
                    <span class="detail-value"><strong>{{ $upcomingEMI->due_date->format('d M Y') }}</strong></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">EMI Amount:</span>
                    <span class="detail-value"><strong>₹{{ number_format($upcomingEMI->amount, 2) }}</strong></span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Penalty Amount:</span>
                    <span class="detail-value">₹{{ number_format($upcomingEMI->penalty_amount, 2) }}</span>
                </div>
                <div class="detail-row">
                    <span class="detail-label">Total Amount Due:</span>
                    <span class="detail-value"><strong style="color: #dc3545;">₹{{ number_format($upcomingEMI->amount + $upcomingEMI->penalty_amount, 2) }}</strong></span>
                </div>
            </div>
        </div>
        @endif

        <div class="section">
            <div class="section-title">🔗 Application Access</div>
            <p>You can access your loan application using the following link:</p>
            <div style="text-align: center;">
                <a href="{{ $applicationUrl }}" class="button">Access Application</a>
            </div>
            <p style="text-align: center; margin-top: 15px;">
                <small>Username: <strong>{{ $user->email }}</strong></small>
            </p>
        </div>
    </div>

    <div class="footer">
        <p>This is an automated email. Please do not reply to this email.</p>
        <p>&copy; {{ date('Y') }} Finance Management System. All rights reserved.</p>
    </div>
</body>
</html>

