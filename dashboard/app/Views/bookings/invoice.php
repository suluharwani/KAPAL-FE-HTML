<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Invoice #<?= $booking['id'] ?></title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
        }
        .invoice-container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
            border: 1px solid #eee;
        }
        .header {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
            border-bottom: 2px solid #333;
            padding-bottom: 10px;
        }
        .company-info {
            text-align: right;
        }
        .invoice-title {
            text-align: center;
            margin: 20px 0;
        }
        .invoice-details {
            display: flex;
            justify-content: space-between;
            margin-bottom: 20px;
        }
        .booking-info, .customer-info {
            width: 48%;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        table, th, td {
            border: 1px solid #ddd;
        }
        th, td {
            padding: 10px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .total {
            text-align: right;
            font-size: 1.2em;
            font-weight: bold;
            margin-top: 20px;
        }
        .footer {
            margin-top: 50px;
            text-align: center;
            font-size: 0.9em;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="invoice-container">
        <div class="header">
            <div>
                <h2>Raja Ampat Boats</h2>
                <p>Jl. Pantai Indah No. 123, Raja Ampat<br>
                Telp: 081234567890<br>
                Email: info@rajaampatboats.com</p>
            </div>
            <div class="company-info">
                <h3>INVOICE</h3>
                <p>No: #<?= $booking['booking_code'] ?><br>
                Tanggal: <?= date('d/m/Y', strtotime($booking['created_at'])) ?></p>
            </div>
        </div>
        
        <div class="invoice-details">
            <div class="booking-info">
                <h4>Detail Perjalanan</h4>
                <p>
                    <strong>Kapal:</strong> <?= esc($booking['boat_name']) ?><br>
                    <strong>Rute:</strong> <?= esc($booking['route_name']) ?><br>
                    <strong>Tanggal:</strong> <?= date('d M Y', strtotime($booking['departure_date'])) ?><br>
                    <strong>Waktu:</strong> <?= $booking['departure_time'] ?>
                </p>
            </div>
            <div class="customer-info">
                <h4>Pelanggan</h4>
                <p>
                    <strong>Nama:</strong> <?= esc($booking['customer_name']) ?><br>
                    <strong>Telepon:</strong> <?= esc($booking['customer_phone']) ?><br>
                    <strong>Email:</strong> <?= esc($booking['customer_email']) ?>
                </p>
            </div>
        </div>
        
        <h4>Detail Penumpang</h4>
        <table>
            <thead>
                <tr>
                    <th>#</th>
                    <th>Nama Lengkap</th>
                    <th>No. Identitas</th>
                    <th>Usia</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($booking['passengers'] as $index => $passenger): ?>
                    <tr>
                        <td><?= $index + 1 ?></td>
                        <td><?= esc($passenger['full_name']) ?></td>
                        <td><?= esc($passenger['identity_number']) ?></td>
                        <td><?= esc($passenger['age']) ?> tahun</td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
        
        <div class="total">
            <p>Total Pembayaran: Rp <?= number_format($booking['total_price'], 0, ',', '.') ?></p>
            <p>Status: <?= ucfirst($booking['status']) ?></p>
        </div>
        
        <div class="footer">
            <p>Terima kasih telah memesan di Raja Ampat Boats</p>
            <p>Silakan tunjukkan invoice ini saat boarding</p>
        </div>
    </div>
</body>
</html>