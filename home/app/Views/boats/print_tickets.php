<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <style>
        @page {
            size: A5;
            margin: 10mm;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 0;
            background-color: #f8f9fa;
        }
        .page {
            page-break-after: always;
        }
        .ticket {
            border: 2px solid #2c3e50;
            border-radius: 8px;
            padding: 12px;
            margin: 8mm 0;
            background: white;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
            page-break-inside: avoid;
        }
        .ticket-header {
            text-align: center;
            border-bottom: 2px solid #3498db;
            padding-bottom: 8px;
            margin-bottom: 12px;
        }
        .ticket-header h2 {
            font-size: 16pt;
            margin: 0;
            color: #2c3e50;
            font-weight: bold;
        }
        .ticket-header h3 {
            font-size: 12pt;
            margin: 4px 0;
            color: #e74c3c;
            font-weight: bold;
        }
        .ticket-body {
            display: flex;
            justify-content: space-between;
            margin-bottom: 12px;
        }
        .ticket-info {
            flex: 2;
            padding-right: 10px;
        }
        .ticket-info p {
            margin: 4px 0;
            font-size: 9pt;
            line-height: 1.3;
        }
        .ticket-info strong {
            color: #2c3e50;
        }
        .ticket-qr {
            flex: 1;
            text-align: center;
            padding: 8px;
            border-left: 1px dashed #ccc;
        }
        .ticket-footer {
            border-top: 2px dashed #95a5a6;
            margin-top: 12px;
            padding-top: 8px;
            text-align: center;
            font-size: 8pt;
            color: #7f8c8d;
        }
        .barcode {
            font-family: 'Libre Barcode 128', monospace;
            font-size: 24pt;
            text-align: center;
            margin: 8px 0;
            letter-spacing: 2px;
        }
        .logo {
            text-align: center;
            margin-bottom: 10px;
            padding: 5px;
            background: linear-gradient(135deg, #3498db, #2c3e50);
            border-radius: 5px;
            color: white;
        }
        .logo-text {
            font-weight: bold;
            font-size: 14pt;
            margin: 0;
        }
        .sub-logo {
            font-size: 8pt;
            margin: 2px 0;
            opacity: 0.9;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 8px;
            border-radius: 12px;
            font-size: 8pt;
            font-weight: bold;
        }
        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .watermark {
            position: absolute;
            opacity: 0.1;
            font-size: 40pt;
            transform: rotate(-45deg);
            z-index: -1;
        }
        .passenger-number {
            background-color: #3498db;
            color: white;
            padding: 2px 6px;
            border-radius: 4px;
            font-size: 8pt;
            margin-right: 5px;
        }
        @media print {
            body {
                background-color: white;
            }
            .ticket {
                box-shadow: none;
                border: 2px solid #000;
            }
        }
    </style>
</head>
<body>
    <?php foreach ($bookings as $booking): ?>
        <?php for ($i = 0; $i < $booking['passenger_count']; $i++): ?>
            <div class="page">
                <div class="logo">
                    <div class="logo-text">RAJA AMPAT BOAT SERVICES</div>
                    <div class="sub-logo">Official E-Ticket</div>
                </div>

                <div class="ticket">
                    <div class="ticket-header">
                        <h2>E-TICKET</h2>
                        <h3>OPEN TRIP JOURNEY</h3>
                    </div>
                    
                    <div class="ticket-body">
                        <div class="ticket-info">
                            <p><strong>Booking Code:</strong> 
                                <span style="font-family: monospace; font-weight: bold; color: #e74c3c;">
                                    <?= esc($booking['booking_code']) ?>
                                </span>
                            </p>
                            
                            <p>
                                <span class="passenger-number">#<?= $i + 1 ?></span>
                                <strong>Passenger:</strong> 
                                <?= isset($booking['passengers'][$i]) ? esc($booking['passengers'][$i]['full_name']) : 'Passenger ' . ($i + 1) ?>
                            </p>
                            
                            <?php if (!empty($open_trip_details)): ?>
                                <p><strong>Route:</strong> 
                                    🚢 <?= esc($open_trip_details['departure_island']) ?> 
                                    → 
                                    <?= esc($open_trip_details['arrival_island']) ?>
                                </p>
                                <p><strong>Departure:</strong> 
                                    📅 <?= date('d M Y', strtotime($open_trip_details['departure_date'])) ?>
                                    ⏰ <?= date('H:i', strtotime($open_trip_details['departure_time'])) ?>
                                </p>
                                <p><strong>Vessel:</strong> ⛵ <?= esc($open_trip_details['boat_name']) ?></p>
                                <p><strong>Boat Type:</strong> <?= esc($open_trip_details['boat_type'] ?? 'Speedboat') ?></p>
                            <?php endif; ?>
                            
                            <p><strong>Total Passengers:</strong> 👥 <?= $booking['passenger_count'] ?> person(s)</p>
                            
                            <p><strong>Status:</strong> 
                                <span class="status-badge status-<?= $booking['booking_status'] ?>">
                                    ✅ <?= ucfirst($booking['booking_status']) ?>
                                </span>
                            </p>
                            
                            <p><strong>Issued Date:</strong> 📅 <?= date('d M Y H:i') ?></p>
                        </div>
                        
                        <div class="ticket-qr">
                            <?php if (!empty($booking['qr_codes'][$i])): ?>
                                <img src="<?= $booking['qr_codes'][$i] ?>" 
                                     width="80" 
                                     height="80" 
                                     style="border: 1px solid #ddd; border-radius: 4px;"
                                     alt="QR Code">
                                <div style="font-size: 7pt; margin-top: 4px; color: #7f8c8d;">
                                    🔍 SCAN TO VERIFY
                                </div>
                            <?php else: ?>
                                <div style="width: 80px; height: 80px; background: #f8f9fa; border: 1px dashed #ccc; 
                                          display: flex; align-items: center; justify-content: center; border-radius: 4px;">
                                    <span style="font-size: 6pt; color: #999;">QR CODE</span>
                                </div>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="barcode">
                        *<?= $booking['booking_code'] ?>-<?= $i + 1 ?>*
                    </div>
                    
                    <div class="ticket-footer">
                        <p>⛵ Thank you for choosing Raja Ampat Boat Services ⛵</p>
                        <p>📞 +62 812-3456-7890 | 🌐 www.rajaampatboats.com</p>
                        <p style="font-size: 7pt; color: #95a5a6;">
                            Please arrive 30 minutes before departure time
                        </p>
                    </div>
                </div>

                <div style="text-align: center; margin-top: 10px; font-size: 8pt; color: #95a5a6;">
                    Page <?= $i + 1 ?> of <?= $booking['passenger_count'] ?> • <?= date('Y-m-d H:i:s') ?>
                </div>
            </div>
        <?php endfor; ?>
    <?php endforeach; ?>
</body>
</html>