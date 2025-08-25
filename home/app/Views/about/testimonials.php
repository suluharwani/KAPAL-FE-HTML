

<!-- Testimonials Hero Section -->
<section class="testimonials-hero py-5 bg-primary text-white">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">Testimonial</h1>
                <p class="lead">Pengalaman nyata dari pelanggan yang telah menggunakan layanan kami</p>
            </div>
        </div>
    </div>
</section>

<!-- Testimonials Content -->
<section class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="fw-bold mb-3">Apa Kata Mereka?</h2>
                <p class="lead">Kepuasan pelanggan adalah prioritas utama kami. Berikut adalah cerita mereka.</p>
            </div>
        </div>

        <!-- Testimonials Grid -->
        <div class="row">
            <?php if (!empty($testimonials)): ?>
                <?php foreach ($testimonials as $testimonial): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card h-100 shadow-sm">
                        <div class="card-body">
                            <div class="text-warning mb-3">
                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                    <?php if ($i <= $testimonial['rating']): ?>
                                        <i class="fas fa-star"></i>
                                    <?php else: ?>
                                        <i class="far fa-star"></i>
                                    <?php endif; ?>
                                <?php endfor; ?>
                                <span class="ms-2 text-muted"><?= $testimonial['rating'] ?>.0</span>
                            </div>
                            
                            <p class="card-text fst-italic">"<?= $testimonial['content'] ?>"</p>
                            
                            <div class="d-flex align-items-center mt-4 pt-3 border-top">
                                <?php if (!empty($testimonial['image'])): ?>
                                <img src="<?= base_url('uploads/testimonials/' . $testimonial['image']) ?>" class="rounded-circle me-3" width="50" height="50" alt="<?= $testimonial['guest_name'] ?>">
                                <?php else: ?>
                                <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center me-3" style="width: 50px; height: 50px;">
                                    <?= substr($testimonial['guest_name'], 0, 1) ?>
                                </div>
                                <?php endif; ?>
                                
                                <div>
                                    <h6 class="mb-0"><?= $testimonial['guest_name'] ?></h6>
                                    <?php if (!empty($testimonial['user_name'])): ?>
                                    <small class="text-muted"><?= $testimonial['user_name'] ?></small>
                                    <?php endif; ?>
                                    <small class="d-block text-muted"><?= date('d M Y', strtotime($testimonial['created_at'])) ?></small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-comments fa-4x text-muted mb-3"></i>
                    <h3>Belum Ada Testimonial</h3>
                    <p class="text-muted">Jadilah yang pertama memberikan testimonial untuk layanan kami</p>
                    <a href="<?= base_url('contact') ?>" class="btn btn-primary">Berikan Testimonial</a>
                </div>
            <?php endif; ?>
        </div>

        <!-- Add Testimonial CTA -->
        <div class="row mt-5">
            <div class="col-lg-8 mx-auto">
                <div class="card bg-light text-center">
                    <div class="card-body py-5">
                        <h3 class="fw-bold mb-3">Bagaimana Pengalaman Anda?</h3>
                        <p class="lead mb-4">Bagikan pengalaman Anda menggunakan layanan Raja Ampat Boat Services</p>
                        <a href="<?= base_url('contact') ?>" class="btn btn-primary btn-lg">Tulis Testimonial</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

