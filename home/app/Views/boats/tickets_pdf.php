<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= esc($title) ?></title>
    <style>
        @page {
            size: A5;
            margin: 0;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 10pt;
            margin: 0;
            padding: 12mm;
            background-color: #ffffff;
            color: #2c3e50;
            width: 190mm;
            min-height: 277mm;
            box-sizing: border-box;
        }
        .container {
            width: 100%;
            display: grid;
            grid-template-columns: repeat(2, 1fr);
            gap: 8mm;
            align-content: start;
        }
        .ticket {
            width: 120mm;
            height: 170mm;
            border: 0px solid #3498db;
            border-radius: 0px;
            padding: 0mm;
            background: linear-gradient(135deg, #ffffff 0%, #f8f9fa 100%);
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            box-sizing: border-box;
            display: flex;
            flex-direction: column;
            position: relative;
            overflow: hidden;
        }
        .ticket::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            height: 4px;
            background: linear-gradient(90deg, #3498db, #2c3e50);
        }
        .ticket-header {
            text-align: center;
            padding-bottom: 3mm;
            margin-bottom: 3mm;
            border-bottom: 2px dashed #bdc3c7;
        }
        .ticket-header h2 {
            font-size: 14pt;
            margin: 0;
            color: #2c3e50;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .ticket-header h3 {
            font-size: 11pt;
            margin: 2mm 0;
            color: #e74c3c;
            font-weight: 600;
        }
        .ticket-body {
            flex-grow: 1;
            display: flex;
            flex-direction: column;
            gap: 2mm;
        }
        .ticket-info {
            flex: 1;
        }
        .info-row {
            display: flex;
            margin-bottom: 2mm;
            align-items: flex-start;
        }
        .info-label {
            font-weight: bold;
            color: #2c3e50;
            font-size: 9pt;
            min-width: 25mm;
            flex-shrink: 0;
        }
        .info-value {
            font-size: 9pt;
            flex-grow: 1;
            word-break: break-word;
        }
        .booking-code {
            font-family: 'Courier New', monospace;
            color: #e74c3c;
            font-weight: bold;
            font-size: 11pt;
            background: #fff3cd;
            padding: 2mm;
            border-radius: 5px;
            border: 2px dashed #ffeeba;
            text-align: center;
            margin: 2mm 0;
        }
        .passenger-badge {
            background: #3498db;
            color: white;
            padding: 1.5mm 3mm;
            border-radius: 15px;
            font-size: 9pt;
            font-weight: bold;
            display: inline-block;
            margin-right: 3mm;
        }
        .ticket-barcode {
            text-align: center;
            padding: 3mm 0;
            margin-top: auto;
            border-top: 2px dashed #bdc3c7;
            background: #f8f9fa;
            border-radius: 5px;
        }
        .ticket-footer {
            text-align: center;
            font-size: 8pt;
            color: #7f8c8d;
            margin-top: 2mm;
            padding: 2mm;
            background: #f8f9fa;
            border-radius: 4px;
        }
        .logo {
            text-align: center;
            margin-bottom: 3mm;
        }
        .logo-text {
            font-weight: 800;
            font-size: 12pt;
            margin: 0;
            color: #2c3e50;
            letter-spacing: 1px;
        }
        .sub-logo {
            font-size: 8pt;
            margin: 1mm 0;
            color: #7f8c8d;
            font-weight: 500;
        }
        .status-badge {
            display: inline-block;
            padding: 2mm 3mm;
            border-radius: 12px;
            font-size: 9pt;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-confirmed {
            background-color: #d4edda;
            color: #155724;
            border: 2px solid #c3e6cb;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
            border: 2px solid #ffeeba;
        }
        .barcode-img {
            width: 100%;
            height: 25mm;
            image-rendering: crisp-edges;
            margin-bottom: 2mm;
            border: 1px solid #dee2e6;
            border-radius: 4px;
            background: white;
            padding: 2mm;
        }
        .barcode-text {
            font-family: 'Courier New', monospace;
            font-size: 8pt;
            font-weight: bold;
            letter-spacing: 0.5px;
            color: #2c3e50;
            background: white;
            padding: 1mm 2mm;
            border-radius: 3px;
            border: 1px solid #dee2e6;
        }
        .route-highlight {
            background: #e8f4fc;
            padding: 3mm;
            border-radius: 6px;
            border-left: 4px solid #3498db;
            margin: 2mm 0;
        }
        .route-text {
            font-weight: bold;
            font-size: 10pt;
            color: #2c3e50;
            text-align: center;
        }
        .watermark {
            position: absolute;
            bottom: 3mm;
            right: 3mm;
            opacity: 0.1;
            font-size: 20pt;
            transform: rotate(-15deg);
            font-weight: 800;
            color: #2c3e50;
        }
        @media print {
            body {
                background-color: white;
                padding: 12mm;
                margin: 0;
                width: 190mm;
                height: 277mm;
            }
            .ticket {
                box-shadow: none;
                border: 2px solid #000;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
            .container {
                gap: 8mm;
            }
        }
    </style>
</head>
<body>
    <div class="container">
    <?php 
    $ticketCount = 0;
    $maxTicketsPerPage = 6; // 3 rows x 2 columns
    
    foreach ($bookings as $booking): 
        for ($i = 0; $i < $booking['passenger_count']; $i++): 
            $ticketCount++;
            
            $barcodeText = $booking['booking_code'] . '-' . ($i + 1);
            $passengerName = isset($booking['passengers'][$i]) ? 
                esc($booking['passengers'][$i]['full_name']) : 
                'Passenger ' . ($i + 1);
    ?>
        <div class="ticket">
            <div class="watermark">RAJA AMPAT</div>
            
            <div class="logo">
                <div class="logo-text">RAJA AMPAT BOAT SERVICES</div>
                <div class="sub-logo">OFFICIAL E-TICKET</div>
            </div>

            <div class="ticket-header">
                <h2>BOAT TICKET</h2>
                <h3>OPEN TRIP JOURNEY</h3>
            </div>
            
            <div class="ticket-body">
                <div class="ticket-info">
                    <div class="booking-code">
                        <?= esc($booking['booking_code']) ?>
                    </div>
                    
                    <div class="info-row">
                        <span class="passenger-badge">PASSENGER #<?= $i + 1 ?></span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Name:</span>
                        <span class="info-value"><?= $passengerName ?></span>
                    </div>
                    
                    <?php if (!empty($open_trip_details)): ?>
                    <div class="route-highlight">
                        <div class="route-text">
                            🚢 <?= esc($open_trip_details['departure_island']) ?> 
                            → 
                            <?= esc($open_trip_details['arrival_island']) ?>
                        </div>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Date:</span>
                        <span class="info-value">📅 <?= date('d M Y', strtotime($open_trip_details['departure_date'])) ?></span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Time:</span>
                        <span class="info-value">⏰ <?= date('H:i', strtotime($open_trip_details['departure_time'])) ?></span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Boat:</span>
                        <span class="info-value">⛵ <?= esc($open_trip_details['boat_name']) ?></span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Type:</span>
                        <span class="info-value"><?= esc($open_trip_details['boat_type'] ?? 'Speedboat') ?></span>
                    </div>
                    <?php endif; ?>
                    
                    <div class="info-row">
                        <span class="info-label">Status:</span>
                        <span class="info-value">
                            <span class="status-badge status-<?= $booking['booking_status'] ?>">
                                <?= ucfirst($booking['booking_status']) ?>
                            </span>
                        </span>
                    </div>
                    
                    <div class="info-row">
                        <span class="info-label">Issued:</span>
                        <span class="info-value"> <?= date('d/m/Y H:i') ?></span>
                    </div>
                </div>
                
                <div class="ticket-barcode">
                    <?php if (!empty($booking['barcodes'][$i])): ?>
                        <img src="<?= $booking['barcodes'][$i] ?>" 
                             class="barcode-img" 
                             alt="Barcode <?= $barcodeText ?>">
                    <?php else: ?>
                        <div style="width: 100%; height: 25mm; background: #ffffff; 
                                  display: flex; align-items: center; justify-content: center;
                                  border: 2px dashed #dee2e6; font-size: 8pt; color: #6c757d;
                                  border-radius: 5px;">
                            <div style="text-align: center;">
                                BARCODE IMAGE<br>
                                <span style="font-family: monospace; font-size: 7pt;"><?= $barcodeText ?></span>
                            </div>
                        </div>
                    <?php endif; ?>
                    <div class="barcode-text">
                        <?= $barcodeText ?>
                    </div>
                </div>
            </div>
            
            <div class="ticket-footer">
                <div>  Thank you for choosing Raja Ampat Boat Services  </div>
                <div>  +62 812-3456-7890 |   www.rajaampatboats.com</div>
            </div>
        </div>
    <?php 
        endfor;
    endforeach; 
    ?>
    </div>
</body>
</html>