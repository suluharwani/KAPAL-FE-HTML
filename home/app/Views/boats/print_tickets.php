<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <style>
        .ticket {
            border: 2px solid #000;
            padding: 20px;
            margin: 10px 0;
            width: 100%;
            max-width: 600px;
            font-family: Arial, sans-serif;
        }
        .ticket-header {
            text-align: center;
            border-bottom: 1px solid #000;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .ticket-body {
            display: flex;
            justify-content: space-between;
        }
        .ticket-info {
            flex: 2;
        }
        .ticket-qr {
            flex: 1;
            text-align: center;
        }
        .ticket-footer {
            border-top: 1px dashed #000;
            margin-top: 15px;
            padding-top: 10px;
            text-align: center;
            font-size: 12px;
        }
        @media print {
            body * {
                visibility: hidden;
            }
            .ticket, .ticket * {
                visibility: visible;
            }
            .ticket {
                position: absolute;
                left: 0;
                top: 0;
                page-break-after: always;
            }
        }
    </style>
</head>
<body>
    <?php foreach ($bookings as $booking): ?>
        <?php for ($i = 0; $i < $booking['passenger_count']; $i++): ?>
            <div class="ticket">
                <div class="ticket-header">
                    <h2>RAJA AMPAT BOAT SERVICES</h2>
                    <h3>E-TICKET</h3>
                </div>
                
                <div class="ticket-body">
                    <div class="ticket-info">
                        <p><strong>Booking Code:</strong> <?= $booking['booking_code'] ?></p>
                        <p><strong>Passenger:</strong> 
                            <?= isset($booking['passengers'][$i]) ? $booking['passengers'][$i]['full_name'] : 'Passenger ' . ($i + 1) ?>
                        </p>
                        
                        <?php if (!empty($open_trip_details)): ?>
                            <p><strong>Route:</strong> 
                                <?= $open_trip_details['departure_island'] ?> - <?= $open_trip_details['arrival_island'] ?>
                            </p>
                            <p><strong>Date:</strong> 
                                <?= date('d M Y', strtotime($open_trip_details['departure_date'])) ?>
                            </p>
                            <p><strong>Time:</strong> 
                                <?= date('H:i', strtotime($open_trip_details['departure_time'])) ?>
                            </p>
                            <p><strong>Boat:</strong> <?= $open_trip_details['boat_name'] ?></p>
                        <?php endif; ?>
                        
                        <p><strong>Status:</strong> <?= ucfirst($booking['booking_status']) ?></p>
                    </div>
                    
                    <div class="ticket-qr">
                        <!-- QR Code akan digenerate oleh JavaScript -->
                        <div id="qrcode-<?= $booking['booking_id'] ?>-<?= $i ?>"></div>
                        <small>Scan to verify</small>
                    </div>
                </div>
                
                <div class="ticket-footer">
                    <p>Thank you for choosing Raja Ampat Boat Services</p>
                    <p>Contact: +62 812-3456-7890 | info@rajaampatboats.com</p>
                </div>
            </div>
        <?php endfor; ?>
    <?php endforeach; ?>

    <!-- Include QRCode library -->
    <script src="https://cdnjs.cloudflare.com/ajax/libs/qrcodejs/1.0.0/qrcode.min.js"></script>
    <script>
        // Generate QR codes for each ticket
        document.addEventListener('DOMContentLoaded', function() {
            <?php foreach ($bookings as $booking): ?>
                <?php for ($i = 0; $i < $booking['passenger_count']; $i++): ?>
                    new QRCode(document.getElementById("qrcode-<?= $booking['booking_id'] ?>-<?= $i ?>"), {
                        text: "<?= $booking['booking_code'] ?>-<?= $i + 1 ?>",
                        width: 80,
                        height: 80
                    });
                <?php endfor; ?>
            <?php endforeach; ?>
            
            // Auto print when page loads
            window.onload = function() {
                window.print();
            };
        });
    </script>
</body>
</html>