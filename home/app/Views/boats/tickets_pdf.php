<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <style>
        @page {
            size: A5;
            margin: 10mm;
        }
        body {
            font-family: Arial, sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 0;
        }
        .ticket {
            border: 1px solid #000;
            padding: 10px;
            margin: 5mm 0;
            page-break-inside: avoid;
        }
        .ticket-header {
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 5px;
            margin-bottom: 8px;
        }
        .ticket-header h2 {
            font-size: 14pt;
            margin: 0;
            color: #2c3e50;
        }
        .ticket-header h3 {
            font-size: 12pt;
            margin: 2px 0;
            color: #e74c3c;
        }
        .ticket-body {
            display: flex;
            justify-content: space-between;
            margin-bottom: 8px;
        }
        .ticket-info {
            flex: 2;
        }
        .ticket-info p {
            margin: 3px 0;
            font-size: 9pt;
        }
        .ticket-qr {
            flex: 1;
            text-align: center;
            padding: 5px;
        }
        .ticket-footer {
            border-top: 1px dashed #000;
            margin-top: 8px;
            padding-top: 5px;
            text-align: center;
            font-size: 8pt;
            color: #7f8c8d;
        }
        .barcode {
            font-family: 'Libre Barcode 128', cursive;
            font-size: 20pt;
            text-align: center;
            margin-top: 5px;
        }
        .logo {
            text-align: center;
            margin-bottom: 5px;
        }
        .logo-text {
            font-weight: bold;
            font-size: 16pt;
            color: #3498db;
        }
    </style>
</head>
<body>
    <div class="logo">
        <div class="logo-text">RAJA AMPAT BOAT SERVICES</div>
    </div>

    <?php foreach ($bookings as $booking): ?>
        <?php for ($i = 0; $i < $booking['passenger_count']; $i++): ?>
            <div class="ticket">
                <div class="ticket-header">
                    <h2>E-TICKET</h2>
                    <h3>OPEN TRIP</h3>
                </div>
                
                <div class="ticket-body">
                    <div class="ticket-info">
                        <p><strong>Booking Code:</strong> <?= $booking['booking_code'] ?></p>
                        <p><strong>Passenger <?= $i + 1 ?>:</strong> 
                            <?= isset($booking['passengers'][$i]) ? $booking['passengers'][$i]['full_name'] : 'Passenger ' . ($i + 1) ?>
                        </p>
                        
                        <?php if (!empty($open_trip_details)): ?>
                            <p><strong>Route:</strong> 
                                <?= $open_trip_details['departure_island'] ?> → <?= $open_trip_details['arrival_island'] ?>
                            </p>
                            <p><strong>Date:</strong> 
                                <?= date('d M Y', strtotime($open_trip_details['departure_date'])) ?>
                            </p>
                            <p><strong>Time:</strong> 
                                <?= date('H:i', strtotime($open_trip_details['departure_time'])) ?>
                            </p>
                            <p><strong>Boat:</strong> <?= $open_trip_details['boat_name'] ?></p>
                        <?php endif; ?>
                        
                        <p><strong>Seats:</strong> <?= $booking['passenger_count'] ?> person(s)</p>
                        <p><strong>Status:</strong> <span style="color: green;"><?= ucfirst($booking['booking_status']) ?></span></p>
                    </div>
                    
                    <div class="ticket-qr">
                        <!-- QR Code placeholder -->
                        <div style="border: 1px solid #ccc; padding: 5px; display: inline-block;">
                            <div style="font-size: 6pt; margin-bottom: 2px;">SCAN QR CODE</div>
                            <div style="width: 60px; height: 60px; background: #f0f0f0; display: flex; align-items: center; justify-content: center;">
                                <span style="font-size: 6pt;">QR CODE</span>
                            </div>
                        </div>
                    </div>
                </div>
                
                <div class="barcode">
                    *<?= $booking['booking_code'] ?>-<?= $i + 1 ?>*
                </div>
                
                <div class="ticket-footer">
                    <p>Thank you for choosing our services</p>
                    <p>Contact: +62 812-3456-7890 | www.rajaampatboats.com</p>
                </div>
            </div>
        <?php endfor; ?>
    <?php endforeach; ?>
</body>
</html>