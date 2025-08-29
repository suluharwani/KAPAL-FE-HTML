<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= $title ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .booking-card {
            border-radius: 15px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            border: none;
        }
        .passenger-form {
            background: #f8f9fa;
            border-radius: 10px;
            padding: 20px;
            margin-bottom: 20px;
        }
        .summary-card {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            border-radius: 15px;
        }
    </style>
</head>
<body>

    <div class="container py-5">
        <div class="row">
            <div class="col-lg-8">
                <div class="card booking-card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h4 class="mb-0"><i class="fas fa-users me-2"></i>Data Penumpang</h4>
                    </div>
                    <div class="card-body">
                        <form id="bookingForm">
                            <input type="hidden" name="schedule_id" value="<?= $schedule['schedule_id'] ?>">
                            
                            <div class="mb-3">
                                <label class="form-label">Jumlah Penumpang</label>
                                <select class="form-select" id="passengerCount" name="passenger_count" required>
                                    <option value="">Pilih jumlah penumpang</option>
                                    <?php for ($i = 1; $i <= min(10, $schedule['available_seats']); $i++): ?>
                                        <option value="<?= $i ?>"><?= $i ?> orang</option>
                                    <?php endfor; ?>
                                </select>
                            </div>

                            <div id="passengerForms" class="passenger-forms">
                                <!-- Passenger forms will be generated here -->
                            </div>

                            <div class="mb-3">
                                <label class="form-label">Metode Pembayaran</label>
                                <select class="form-select" name="payment_method" required>
                                    <option value="">Pilih metode pembayaran</option>
                                    <option value="transfer">Transfer Bank</option>
                                    <option value="cash">Bayar di Tempat</option>
                                </select>
                            </div>

                            <button type="submit" class="btn btn-primary btn-lg w-100">
                                <i class="fas fa-check me-2"></i>Pesan Sekarang
                            </button>
                        </form>
                    </div>
                </div>
            </div>

            <div class="col-lg-4">
                <div class="card summary-card">
                    <div class="card-body">
                        <h5 class="card-title">Detail Perjalanan</h5>
                        
                        <div class="mb-3">
                            <strong>Kapal:</strong><br>
                            <?= $schedule['boat_name'] ?> (<?= $schedule['boat_type'] ?>)
                        </div>
                        
                        <div class="mb-3">
                            <strong>Rute:</strong><br>
                            <?= $schedule['departure_island'] ?> → <?= $schedule['arrival_island'] ?>
                        </div>
                        
                        <div class="mb-3">
                            <strong>Keberangkatan:</strong><br>
                            <?= date('d M Y', strtotime($schedule['departure_date'])) ?><br>
                            <?= date('H:i', strtotime($schedule['departure_time'])) ?> WIT
                        </div>
                        
                        <div class="mb-3">
                            <strong>Durasi:</strong><br>
                            <?= $schedule['estimated_duration'] ?> jam
                        </div>
                        
                        <div class="mb-3">
                            <strong>Harga per orang:</strong><br>
                            Rp <?= number_format($schedule['price_per_trip'], 0, ',', '.') ?>
                        </div>
                        
                        <hr style="border-color: rgba(255,255,255,0.3)">
                        
                        <div class="d-flex justify-content-between">
                            <strong>Total:</strong>
                            <strong id="totalPrice">Rp 0</strong>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function() {
            const passengerCount = document.getElementById('passengerCount');
            const passengerForms = document.getElementById('passengerForms');
            const totalPrice = document.getElementById('totalPrice');
            const pricePerPerson = <?= $schedule['price_per_trip'] ?>;

            passengerCount.addEventListener('change', function() {
                const count = parseInt(this.value);
                generatePassengerForms(count);
                updateTotalPrice(count);
            });

            function generatePassengerForms(count) {
                passengerForms.innerHTML = '';
                
                for (let i = 1; i <= count; i++) {
                    const form = `
                        <div class="passenger-form mb-3">
                            <h6 class="mb-3">Penumpang ${i}</h6>
                            <div class="row g-3">
                                <div class="col-md-6">
                                    <input type="text" class="form-control" name="passengers[${i-1}][name]" 
                                           placeholder="Nama Lengkap" required>
                                </div>
                                <div class="col-md-6">
                                    <input type="number" class="form-control" name="passengers[${i-1}][identity]" 
                                           placeholder="No. KTP (opsional)">
                                </div>
                                <div class="col-md-6">
                                    <input type="tel" class="form-control" name="passengers[${i-1}][phone]" 
                                           placeholder="No. HP (opsional)">
                                </div>
                                <div class="col-md-6">
                                    <input type="number" class="form-control" name="passengers[${i-1}][age]" 
                                           placeholder="Usia (opsional)" min="1" max="100">
                                </div>
                            </div>
                        </div>
                    `;
                    passengerForms.innerHTML += form;
                }
            }

            function updateTotalPrice(count) {
                totalPrice.textContent = 'Rp ' + (count * pricePerPerson).toLocaleString('id-ID');
            }

            document.getElementById('bookingForm').addEventListener('submit', async function(e) {
    e.preventDefault();
    
    // Show loading state
    const submitBtn = this.querySelector('button[type="submit"]');
    const originalText = submitBtn.innerHTML;
    submitBtn.innerHTML = '<i class="fas fa-spinner fa-spin me-2"></i>Memproses...';
    submitBtn.disabled = true;

    const formData = new FormData(this);
    const passengers = [];
    
    // Collect passenger data
    const passengerInputs = this.querySelectorAll('input[name^="passengers"]');
    for (let i = 0; i < passengerInputs.length; i += 4) {
        passengers.push({
            name: passengerInputs[i].value,
            identity: passengerInputs[i+1]?.value || '',
            phone: passengerInputs[i+2]?.value || '',
            age: passengerInputs[i+3]?.value || ''
        });
    }
    
    formData.set('passengers', JSON.stringify(passengers));
    
    try {
        const response = await fetch('/booking/process', {
            method: 'POST',
            body: formData,
            headers: {
                'X-Requested-With': 'XMLHttpRequest'
            }
        });
        
        // Check if response is JSON
        const contentType = response.headers.get('content-type');
        if (!contentType || !contentType.includes('application/json')) {
            const text = await response.text();
            console.error('Non-JSON response:', text.substring(0, 200));
            throw new Error('Server mengembalikan response yang tidak valid');
        }
        
        const result = await response.json();
        
        if (result.status === 'success') {
            window.location.href = '/booking/success/' + result.booking_code;
        } else {
            // Show error message
            let errorMessage = result.message || 'Terjadi kesalahan';
            if (result.errors) {
                errorMessage += '\n' + Object.values(result.errors).join('\n');
            }
            alert('Error: ' + errorMessage);
        }
    } catch (error) {
        console.error('Error:', error);
        alert('Terjadi kesalahan: ' + error.message);
    } finally {
        // Restore button state
        submitBtn.innerHTML = originalText;
        submitBtn.disabled = false;
    }
});
        });
    </script>
</body>
</html>