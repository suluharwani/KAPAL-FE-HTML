<div class="container my-5">
    <h1 class="mb-4">Hubungi Kami</h1>
    
    <div class="row">
        <div class="col-md-6 mb-5 mb-md-0">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Kirim Pesan</h3>
                </div>
                <div class="card-body">
                    <?php if (session()->getFlashdata('message')): ?>
                        <div class="alert alert-success"><?= session()->getFlashdata('message') ?></div>
                    <?php endif; ?>
                    
                    <form action="<?= base_url('contact/submit') ?>" method="post">
                        <div class="mb-3">
                            <label for="name" class="form-label">Nama Lengkap</label>
                            <input type="text" class="form-control" id="name" name="name" required>
                        </div>
                        <div class="mb-3">
                            <label for="email" class="form-label">Email</label>
                            <input type="email" class="form-control" id="email" name="email" required>
                        </div>
                        <div class="mb-3">
                            <label for="phone" class="form-label">Nomor Telepon</label>
                            <input type="tel" class="form-control" id="phone" name="phone">
                        </div>
                        <div class="mb-3">
                            <label for="subject" class="form-label">Subjek</label>
                            <input type="text" class="form-control" id="subject" name="subject" required>
                        </div>
                        <div class="mb-3">
                            <label for="message" class="form-label">Pesan</label>
                            <textarea class="form-control" id="message" name="message" rows="5" required></textarea>
                        </div>
                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary">Kirim Pesan</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
        
        <div class="col-md-6">
            <div class="card shadow">
                <div class="card-header bg-primary text-white">
                    <h3 class="mb-0">Informasi Kontak</h3>
                </div>
                <div class="card-body">
                    <div class="mb-4">
                        <h4><i class="fas fa-map-marker-alt text-primary me-2"></i> Alamat Kantor</h4>
                        <p class="ms-4 ps-3">Jl. Raya Waigeo No. 45<br>Kota Waisai, Kabupaten Raja Ampat<br>Papua Barat, Indonesia</p>
                    </div>
                    
                    <div class="mb-4">
                        <h4><i class="fas fa-phone-alt text-primary me-2"></i> Telepon</h4>
                        <p class="ms-4 ps-3">
                            Customer Service: +62 812-3456-7890<br>
                            Reservasi: +62 812-3456-7891<br>
                            Operasional: +62 812-3456-7892
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <h4><i class="fas fa-envelope text-primary me-2"></i> Email</h4>
                        <p class="ms-4 ps-3">
                            info@rajaampatboats.com<br>
                            booking@rajaampatboats.com<br>
                            support@rajaampatboats.com
                        </p>
                    </div>
                    
                    <div class="mb-4">
                        <h4><i class="fas fa-clock text-primary me-2"></i> Jam Operasional</h4>
                        <p class="ms-4 ps-3">
                            Senin - Jumat: 08:00 - 17:00 WIT<br>
                            Sabtu: 08:00 - 15:00 WIT<br>
                            Minggu: Tutup
                        </p>
                    </div>
                    
                    <div class="mt-4">
                        <h5 class="mb-3">Media Sosial</h5>
                        <div class="social-icons">
                            <a href="#" class="btn btn-outline-primary btn-sm me-2"><i class="fab fa-facebook-f"></i></a>
                            <a href="#" class="btn btn-outline-primary btn-sm me-2"><i class="fab fa-instagram"></i></a>
                            <a href="#" class="btn btn-outline-primary btn-sm me-2"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="btn btn-outline-primary btn-sm"><i class="fab fa-youtube"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>