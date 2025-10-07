<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>
<body>

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-6">
                <div class="card border-success">
                    <div class="card-body text-center py-5">
                        <div class="text-success mb-4">
                            <i class="fas fa-check-circle fa-5x"></i>
                        </div>
                        
                        <h2 class="card-title text-success mb-3">Booking Successful!</h2>
                        <p class="card-text mb-4">
                            Your booking with code <strong><?= $booking['booking_code'] ?></strong> has been successfully created.
                        </p>
                        
                        <div class="alert alert-info mb-4">
                            <h6>Booking Details:</h6>
                            <p class="mb-1">Boat: <?= $booking['boat_name'] ?></p>
                            <p class="mb-1">Route: <?= $booking['departure_island'] ?> → <?= $booking['arrival_island'] ?></p>
                            <p class="mb-1">Date: <?= date('d M Y', strtotime($booking['departure_date'])) ?></p>
                            <p class="mb-1">Time: <?= date('H:i', strtotime($booking['departure_time'])) ?> WIT</p>
                            <p class="mb-0">Total: Rp <?= number_format($booking['total_price'], 0, ',', '.') ?></p>
                        </div>

                        <div class="d-grid gap-2 d-md-flex justify-content-md-center">
                            <a href="/booking/print/<?= $booking['booking_code'] ?>" class="btn btn-primary me-md-2">
                                <i class="fas fa-print me-2"></i>Print Ticket
                            </a>
                            <a href="/booking/my-bookings" class="btn btn-outline-secondary">
                                <i class="fas fa-list me-2"></i>View My Bookings
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</body>
</html>