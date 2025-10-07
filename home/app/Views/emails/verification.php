<!DOCTYPE html>
<html>
<head>
    <title>Email Verification</title>
    <style>
        body { font-family: Arial, sans-serif; line-height: 1.6; }
        .container { max-width: 600px; margin: 0 auto; padding: 20px; }
        .button {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            border-radius: 5px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Verify Your Email</h2>
        <p>Hello <?= $user['full_name'] ?>,</p>
        <p>Thank you for registering at Raja Ampat Boat Services. Please click the button below to verify your email address:</p>
        
        <p>
            <a href="<?= $verificationLink ?>" class="button">Verify Email</a>
        </p>
        
        <p>If the button above doesn't work, copy and paste the following link into your browser:</p>
        <p><?= $verificationLink ?></p>
        
        <p>The verification link will expire in 1 hour.</p>
        
        <p>Best regards,<br>Raja Ampat Boat Services Team</p>
    </div>
</body>
</html>