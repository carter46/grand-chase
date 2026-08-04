<!DOCTYPE html>
<html>
<head>
    <title>Transaction Export</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background-color: #f8f8f8; border-left: 4px solid #4f46e5; padding: 15px; margin-bottom: 20px;">
        <h2 style="margin-top: 0; color: #4f46e5;">Your Transaction Export</h2>
    </div>
    
    <p>Hello {{ $user->name }},</p>
    
    <p>You recently requested an export of your transaction data. Your {{ $exportType }} file is attached to this email.</p>
    
    <p>If you did not request this export or have any questions, please contact our support team.</p>
    
    <div style="margin-top: 30px; padding-top: 20px; border-top: 1px solid #eee; font-size: 14px; color: #666;">
        <p>Thank you for choosing our services.</p>
        <p>Best regards,<br>The Support Team</p>
    </div>
</body>
</html> 