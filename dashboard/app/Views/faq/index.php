<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>FAQ<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Frequently Asked Questions</h5>
                <div>
                    <a href="<?= base_url('faq/featured') ?>" class="btn btn-sm btn-info me-2">
                        <i class="bi bi-star"></i> Lihat Unggulan
                    </a>
                    <?php if (session()->get('role') === 'admin'): ?>
                        <a href="<?= base_url('faq/add') ?>" class="btn btn-sm btn-primary">
                            <i class="bi bi-plus"></i> Tambah
                        </a>
                    <?php endif; ?>
                </div>
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
                
                <!-- Category Filter -->
                <div class="mb-4">
                    <div class="btn-group" role="group">
                        <a href="<?= base_url('faq') ?>" class="btn btn-outline-secondary active">
                            Semua Kategori
                        </a>
                        <?php foreach ($categories as $category): ?>
                            <a href="<?= base_url('faq?category=' . urlencode($category)) ?>" 
                               class="btn btn-outline-secondary">
                                <?= ucfirst($category) ?>
                            </a>
                        <?php endforeach; ?>
                    </div>
                </div>
                
                <!-- FAQ List -->
                <?php if (empty($faqs)): ?>
                    <div class="alert alert-info">
                        Tidak ada FAQ yang tersedia.
                    </div>
                <?php else: ?>
                    <div class="accordion" id="faqAccordion">
                        <?php foreach ($faqs as $index => $faq): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="heading<?= $index ?>">
                                    <button class="accordion-button collapsed" type="button" 
                                            data-bs-toggle="collapse" data-bs-target="#collapse<?= $index ?>" 
                                            aria-expanded="false" aria-controls="collapse<?= $index ?>">
                                        <?= esc($faq['question']) ?>
                                        <span class="badge bg-primary ms-2"><?= esc($faq['category']) ?></span>
                                        <?php if ($faq['is_featured']): ?>
                                            <span class="badge bg-warning ms-2">Unggulan</span>
                                        <?php endif; ?>
                                    </button>
                                </h2>
                                <div id="collapse<?= $index ?>" class="accordion-collapse collapse" 
                                     aria-labelledby="heading<?= $index ?>" data-bs-parent="#faqAccordion">
                                    <div class="accordion-body">
                                        <?= nl2br(esc($faq['answer'])) ?>
                                        
                                        <?php if (session()->get('role') === 'admin'): ?>
                                            <div class="mt-3 d-flex justify-content-end">
                                                <a href="<?= base_url('faq/edit/' . $faq['id']) ?>" 
                                                   class="btn btn-sm btn-warning me-2">
                                                    <i class="bi bi-pencil"></i> Edit
                                                </a>
                                                <a href="<?= base_url('faq/delete/' . $faq['id']) ?>" 
                                                   class="btn btn-sm btn-danger" 
                                                   onclick="return confirm('Apakah Anda yakin ingin menghapus FAQ ini?')">
                                                    <i class="bi bi-trash"></i> Hapus
                                                </a>
                                            </div>
                                        <?php endif; ?>
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