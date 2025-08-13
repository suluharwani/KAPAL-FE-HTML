<!DOCTYPE html>
<html>
<head>
    <title>Verifikasi Email</title>
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
        <h2>Verifikasi Email Anda</h2>
        <p>Halo <?= $user['full_name'] ?>,</p>
        <p>Terima kasih telah mendaftar di Raja Ampat Boat Services. Silakan klik tombol di bawah ini untuk memverifikasi alamat email Anda:</p>
        
        <p>
            <a href="<?= $verificationLink ?>" class="button">Verifikasi Email</a>
        </p>
        
        <p>Jika tombol di atas tidak bekerja, salin dan tempel link berikut di browser Anda:</p>
        <p><?= $verificationLink ?></p>
        
        <p>Link verifikasi akan kadaluarsa dalam 1 jam.</p>
        
        <p>Salam,<br>Tim Raja Ampat Boat Services</p>
    </div>
</body>
</html>