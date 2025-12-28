<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>EMI Payment Reminder</title>
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
            border-radius: 0 0 10px 10px;
        }
        .alert {
            padding: 15px;
            margin: 20px 0;
            border-radius: 5px;
            border-left: 4px solid;
        }
        .alert-warning {
            background-color: #fff3cd;
            border-color: #ffc107;
            color: #856404;
        }
        .alert-danger {
            background-color: #f8d7da;
            border-color: #dc3545;
            color: #721c24;
        }
        .details-box {
            background: white;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .detail-row {
            display: flex;
            justify-content: space-between;
            padding: 10px 0;
            border-bottom: 1px solid #eee;
        }
        .detail-row:last-child {
            border-bottom: none;
        }
        .detail-label {
            font-weight: bold;
            color: #666;
        }
        .detail-value {
            color: #333;
            font-size: 1.1em;
        }
        .amount-highlight {
            color: #dc3545;
            font-size: 1.3em;
            font-weight: bold;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #ddd;
            color: #666;
            font-size: 0.9em;
        }
        .button {
            display: inline-block;
            padding: 12px 30px;
            background: #667eea;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
            font-weight: bold;
        }
        .button:hover {
            background: #5568d3;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>EMI Payment Reminder</h1>
        <p>LaraFinance - Loan Management System</p>
    </div>
    
    <div class="content">
        <p>Dear <strong><?php echo e($user->name); ?></strong>,</p>
        
        <?php if($isOverdue): ?>
        <div class="alert alert-danger">
            <strong>⚠️ Overdue Payment!</strong> Your EMI payment is past due. Please make the payment immediately to avoid additional penalties.
        </div>
        <?php else: ?>
        <div class="alert alert-warning">
            <strong>📅 Upcoming Payment!</strong> This is a friendly reminder about your upcoming EMI payment.
        </div>
        <?php endif; ?>
        
        <div class="details-box">
            <h3 style="margin-top: 0;">Payment Details</h3>
            
            <div class="detail-row">
                <span class="detail-label">Loan ID:</span>
                <span class="detail-value">#<?php echo e($loan->id); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Due Date:</span>
                <span class="detail-value"><strong><?php echo e($installment->due_date->format('F d, Y')); ?></strong></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">EMI Amount:</span>
                <span class="detail-value">₹<?php echo e(number_format($installment->amount, 2)); ?></span>
            </div>
            
            <?php if($installment->penalty_amount > 0): ?>
            <div class="detail-row">
                <span class="detail-label">Penalty Amount:</span>
                <span class="detail-value" style="color: #dc3545;">₹<?php echo e(number_format($installment->penalty_amount, 2)); ?></span>
            </div>
            <?php endif; ?>
            
            <div class="detail-row" style="background: #f0f0f0; padding: 15px; margin: 10px -20px -20px -20px; border-radius: 0 0 5px 5px;">
                <span class="detail-label" style="font-size: 1.1em;">Total Amount Due:</span>
                <span class="amount-highlight">₹<?php echo e(number_format($totalAmount, 2)); ?></span>
            </div>
        </div>
        
        <div class="details-box">
            <h3 style="margin-top: 0;">Loan Summary</h3>
            
            <div class="detail-row">
                <span class="detail-label">Principal Amount:</span>
                <span class="detail-value">₹<?php echo e(number_format($loan->principal_amount, 2)); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Interest Rate:</span>
                <span class="detail-value"><?php echo e(number_format($loan->interest_rate, 2)); ?>%</span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Monthly EMI:</span>
                <span class="detail-value">₹<?php echo e(number_format($loan->emi_amount, 2)); ?></span>
            </div>
            
            <div class="detail-row">
                <span class="detail-label">Loan Status:</span>
                <span class="detail-value">
                    <span style="padding: 5px 10px; background: <?php echo e($loan->status === 'active' ? '#28a745' : '#6c757d'); ?>; color: white; border-radius: 3px;">
                        <?php echo e(ucfirst($loan->status)); ?>

                    </span>
                </span>
            </div>
        </div>
        
        <p style="text-align: center;">
            <a href="<?php echo e(config('app.url')); ?>/home" class="button">Make Payment Now</a>
        </p>
        
        <p style="margin-top: 30px;">
            <strong>Important:</strong> Please ensure your payment is made before the due date to avoid late fees and penalties. 
            If you have already made the payment, please ignore this reminder.
        </p>
        
        <p>
            If you have any questions or concerns, please contact our support team.
        </p>
        
        <div class="footer">
            <p>This is an automated reminder from LaraFinance.</p>
            <p>© <?php echo e(date('Y')); ?> LaraFinance. All rights reserved.</p>
        </div>
    </div>
</body>
</html>

<?php /**PATH D:\ARTtoframe_rahul\finance\resources\views/emails/installment-reminder.blade.php ENDPATH**/ ?>