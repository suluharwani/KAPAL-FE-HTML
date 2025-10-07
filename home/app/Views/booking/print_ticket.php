<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        @media print {
            body * {
                visibility: hidden;
            }
            .ticket-container, .ticket-container * {
                visibility: visible;
            }
            .ticket-container {
                position: absolute;
                left: 0;
                top: 0;
                width: 100%;
            }
            .no-print {
                display: none !important;
            }
        }
        .ticket {
            border: 2px solid #000;
            border-radius: 10px;
            padding: 20px;
            max-width: 400px;
            margin: 0 auto;
            background: white;
        }
        .ticket-header {
            border-bottom: 2px dashed #000;
            padding-bottom: 15px;
            margin-bottom: 15px;
        }
        .barcode {
            font-family: 'Libre Barcode 128', cursive;
            font-size: 2em;
            text-align: center;
        }
    </style>
</head>
<body>
    <div class="container py-4 ticket-container">
        <div class="text-center no-print mb-3">
            <button onclick="window.print()" class="btn btn-primary me-2">
                <i class="fas fa-print me-1"></i>Print
            </button>
            <a href="/booking/my-bookings" class="btn btn-secondary">
                <i class="fas fa-arrow-left me-1"></i>Back
            </a>
        </div>

        <div class="ticket">
            <div class="ticket-header text-center">
                <h4 class="mb-1">RAJA AMPAT BOAT SERVICES</h4>
                <p class="mb-0">E-Ticket</p>
                <small><?= $booking['booking_code'] ?></small>
            </div>

            <div class="row mb-3">
                <div class="col-6">
                    <strong>Boat:</strong><br>
                    <?= $booking['boat_name'] ?>
                </div>
                <div class="col-6">
                    <strong>Date:</strong><br>
                    <?= date('d/m/Y', strtotime($booking['departure_date'])) ?>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-6">
                    <strong>Departure:</strong><br>
                    <?= $booking['departure_island'] ?><br>
                    <?= date('H:i', strtotime($booking['departure_time'])) ?> WIT
                </div>
                <div class="col-6">
                    <strong>Destination:</strong><br>
                    <?= $booking['arrival_island'] ?>
                </div>
            </div>

            <div class="mb-3">
                <strong>Passengers:</strong><br>
                <?= $booking['passenger_count'] ?> person<?= $booking['passenger_count'] > 1 ? 's' : '' ?>
            </div>

            <div class="mb-3">
                <strong>Status:</strong><br>
                <span class="badge bg-success"><?= strtoupper($booking['booking_status']) ?></span>
            </div>

            <div class="barcode mb-3">
                *<?= $booking['booking_code'] ?>*
            </div>

            <div class="text-center">
                <small>
                    This ticket is valid and can be used for boarding.<br>
                    Please show this ticket during boarding.
                </small>
            </div>
        </div>
    </div>

    <script>
        // Auto print when page loads
        window.onload = function() {
            window.print();
        }
    </script>
</body>
</html>