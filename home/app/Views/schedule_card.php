<?php
// Berikan nilai default jika $type tidak ada
$type = $type ?? 'regular';
$isOpenTrip = $type === 'open_trip';
$schedule = $schedule ?? [];
?>

<?php if (!empty($schedule)): ?>
<div class="col-md-6 col-lg-4 mb-4">
    <div class="card h-100 result-card <?= $isOpenTrip ? 'open-trip-card' : 'regular-trip-card' ?>">
        <span class="trip-badge badge <?= $isOpenTrip ? 'open-trip-badge' : 'regular-trip-badge' ?>">
            <i class="fas <?= $isOpenTrip ? 'fa-users' : 'fa-calendar-day' ?> me-1"></i>
            <?= $isOpenTrip ? 'OPEN TRIP' : 'REGULAR' ?>
        </span>
        
        <?php if (!empty($schedule['is_featured'])): ?>
            <span class="feature-badge badge">
                <i class="fas fa-star me-1"></i>Featured
            </span>
        <?php endif; ?>
        
        <img src="<?= !empty($schedule['image_url']) ? $schedule['image_url'] : 'https://images.unsplash.com/photo-1530533718754-001d2668365a?ixlib=rb-4.0.3&auto=format&fit=crop&w=500&q=80' ?>" 
             class="boat-img card-img-top" 
             alt="<?= !empty($schedule['boat_name']) ? $schedule['boat_name'] : 'Kapal' ?>">
             
        <div class="card-body">
            <h5 class="card-title"><?= !empty($schedule['boat_name']) ? $schedule['boat_name'] : 'Nama Kapal' ?></h5>
            <h6 class="card-subtitle mb-2 text-muted">
                <?= !empty($schedule['departure_island']) ? $schedule['departure_island'] : 'Pulau Keberangkatan' ?> → 
                <?= !empty($schedule['arrival_island']) ? $schedule['arrival_island'] : 'Pulau Tujuan' ?>
            </h6>
            
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">
                    <i class="fas fa-calendar-alt me-1"></i> 
                    <?= !empty($schedule['departure_date']) ? date('d M Y', strtotime($schedule['departure_date'])) : 'Tanggal' ?>
                </span>
                <span class="text-muted">
                    <i class="fas fa-clock me-1"></i> 
                    <?= !empty($schedule['departure_time']) ? date('H:i', strtotime($schedule['departure_time'])) : 'Waktu' ?>
                </span>
            </div>
            
            <div class="d-flex justify-content-between align-items-center mb-2">
                <span class="text-muted">
                    <i class="fas fa-users me-1"></i> 
                    <?= !empty($schedule['available_seats']) ? $schedule['available_seats'] : '0' ?> kursi tersedia
                </span>
                <span class="text-muted">
                    <i class="fas fa-hourglass-half me-1"></i> 
                    <?= !empty($schedule['estimated_duration']) ? $schedule['estimated_duration'] : '0' ?> jam
                </span>
            </div>
            
            <?php if ($isOpenTrip && !empty($schedule['price_per_person'])): ?>
                <div class="alert alert-info py-2 mb-3">
                    <small>
                        <i class="fas fa-info-circle me-1"></i>
                        Harga per orang: <strong>Rp <?= number_format($schedule['price_per_person'], 0, ',', '.') ?></strong>
                    </small>
                </div>
            <?php endif; ?>
            
            <div class="d-flex justify-content-between align-items-center mt-3">
                <span class="price-tag <?= $isOpenTrip ? 'open-trip-price' : '' ?>">
                    Rp <?= number_format(
                        ($isOpenTrip && !empty($schedule['price_per_person'])) ? 
                        $schedule['price_per_person'] : 
                        (!empty($schedule['price_per_trip']) ? $schedule['price_per_trip'] : 0), 
                        0, ',', '.'
                    ) ?>
                </span>
                <button class="btn <?= $isOpenTrip ? 'btn-danger' : 'btn-primary' ?> btn-sm book-btn" 
        data-schedule-id="<?= $schedule['schedule_id'] ?>"
        data-boat-name="<?= $schedule['boat_name'] ?>"
        data-is-open-trip="<?= $isOpenTrip ? '1' : '0' ?>">
    <?= $isOpenTrip ? 'Join Trip' : 'Pesan Sekarang' ?>
</button>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>