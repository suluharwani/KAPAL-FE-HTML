<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <style>
        body { font-family: Arial, sans-serif; margin: 20px; }
        .ticket { border: 2px solid #000; padding: 20px; max-width: 600px; margin: 0 auto; }
        .header { text-align: center; margin-bottom: 20px; }
        .section { margin-bottom: 15px; }
        .section-title { font-weight: bold; border-bottom: 1px solid #ccc; padding-bottom: 5px; margin-bottom: 10px; }
        table { width: 100%; border-collapse: collapse; }
        table, th, td { border: 1px solid #ccc; }
        th, td { padding: 8px; text-align: left; }
        .barcode { text-align: center; margin-top: 20px; }
        @media print {
            body { margin: 0; }
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="ticket">
        <div class="header">
            <h1>RAJA AMPAT BOAT SERVICES</h1>
            <h2>TIKET OPEN TRIP</h2>
        </div>
        
        <div class="section">
            <div class="section-title">Informasi Booking</div>
            <table>
                <tr>
                    <th>Kode Booking</th>
                    <td><?= $booking['booking_code'] ?></td>
                </tr>
                <tr>
                    <th>Rute</th>
                    <td><?= $booking['departure_island'] ?> - <?= $booking['arrival_island'] ?></td>
                </tr>
                <tr>
                    <th>Tanggal</th>
                    <td><?= date('d M Y', strtotime($booking['departure_date'])) ?></td>
                </tr>
                <tr>
                    <th>Waktu</th>
                    <td><?= date('H:i', strtotime($booking['departure_time'])) ?></td>
                </tr>
                <tr>
                    <th>Kapal</th>
                    <td><?= $booking['boat_name'] ?></td>
                </tr>
            </table>
        </div>
        
        <div class="section">
            <div class="section-title">Daftar Penumpang</div>
            <table>
                <thead>
                    <tr>
                        <th>No</th>
                        <th>Nama</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($passengers as $index => $passenger): ?>
                        <tr>
                            <td><?= $index + 1 ?></td>
                            <td><?= $passenger['full_name'] ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
        
        <div class="barcode">
            <div>Kode Booking: <?= $booking['booking_code'] ?></div>
            <!-- You can add barcode generation here if needed -->
        </div>
        
        <div class="no-print" style="margin-top: 20px; text-align: center;">
            <button onclick="window.print()" class="btn btn-primary">Cetak Tiket</button>
            <button onclick="window.close()" class="btn btn-secondary">Tutup</button>
        </div>
    </div>
</body>
</html>