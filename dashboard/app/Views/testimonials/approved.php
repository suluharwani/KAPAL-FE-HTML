<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>Testimoni Disetujui<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Testimoni Disetujui</h5>
                <a href="<?= base_url('testimonials') ?>" class="btn btn-sm btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali
                </a>
            </div>
            <div class="card-body">
                <?php if (session()->getFlashdata('success')): ?>
                    <div class="alert alert-success">
                        <?= session()->getFlashdata('success') ?>
                    </div>
                <?php endif; ?>
                
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger">
                        <?= $error ?>
                    </div>
                <?php endif; ?>
                
                <?php if (empty($testimonials)): ?>
                    <div class="alert alert-info">
                        Tidak ada testimoni yang disetujui.
                    </div>
                <?php else: ?>
                    <div class="row">
                        <?php foreach ($testimonials as $testimonial): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card h-100">
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between mb-3">
                                            <h6 class="card-title mb-0"><?= esc($testimonial['user_name']) ?></h6>
                                            <div>
                                                <?php for ($i = 1; $i <= 5; $i++): ?>
                                                    <i class="bi bi-star<?= $i <= $testimonial['rating'] ? '-fill text-warning' : '' ?>"></i>
                                                <?php endfor; ?>
                                            </div>
                                        </div>
                                        <p class="card-text"><?= esc($testimonial['content']) ?></p>
                                    </div>
                                    <div class="card-footer bg-transparent">
                                        <small class="text-muted">
                                            <?= date('d M Y', strtotime($testimonial['created_at'])) ?>
                                        </small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>
<?= $this->endSection() ?>