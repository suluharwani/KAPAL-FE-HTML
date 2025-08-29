<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Check-in Kapal</h5>
    </div>
    <div class="card-body">
        <?php if (session()->getFlashdata('success')): ?>
            <div class="alert alert-success"><?= session()->getFlashdata('success') ?></div>
        <?php endif; ?>
        
        <?php if (session()->getFlashdata('error')): ?>
            <div class="alert alert-danger"><?= session()->getFlashdata('error') ?></div>
        <?php endif; ?>

        <form action="<?= base_url('admin/checkin/process') ?>" method="post">
            <div class="row">
                <div class="col-md-8">
                    <div class="mb-3">
                        <label for="booking_code" class="form-label">Kode Booking</label>
                        <input type="text" class="form-control" id="booking_code" name="booking_code" 
                               placeholder="Contoh: BOOK-68B115F8AC05B-1" required>
                        <div class="form-text">
                            Format: KodeBooking-JumlahPenumpang (contoh: BOOK-68B115F8AC05B-2 untuk 2 penumpang)
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="mb-3">
                        <label class="form-label">&nbsp;</label>
                        <button type="submit" class="btn btn-primary w-100">Process Check-in</button>
                    </div>
                </div>
            </div>
        </form>

        <hr>

        <h6>Petunjuk Check-in:</h6>
        <ul>
            <li>Masukkan kode booking diikuti dengan jumlah penumpang (contoh: BOOK-ABC123-2)</li>
            <li>Jika tidak ditentukan jumlah, default 1 penumpang</li>
            <li>Sistem akan mengurangi kapasitas kursi yang tersedia</li>
            <li>Check-in hanya bisa dilakukan untuk booking yang statusnya confirmed atau paid</li>
        </ul>
    </div>
</div>

<?= $this->include('templates/admin_footer') ?>