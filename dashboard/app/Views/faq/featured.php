<?= $this->extend('templates/dashboard') ?>

<?= $this->section('title') ?>FAQ Unggulan<?= $this->endSection() ?>

<?= $this->section('content') ?>
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">FAQ Unggulan</h5>
                <a href="<?= base_url('faq') ?>" class="btn btn-sm btn-secondary">
                    <i class="bi bi-arrow-left"></i> Kembali ke FAQ
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
                
                <?php if (empty($featuredFaqs)): ?>
                    <div class="alert alert-info">
                        Tidak ada FAQ unggulan yang tersedia.
                    </div>
                <?php else: ?>
                    <div class="accordion" id="featuredFaqAccordion">
                        <?php foreach ($featuredFaqs as $index => $faq): ?>
                            <div class="accordion-item">
                                <h2 class="accordion-header" id="featuredHeading<?= $index ?>">
                                    <button class="accordion-button" type="button" 
                                            data-bs-toggle="collapse" data-bs-target="#featuredCollapse<?= $index ?>" 
                                            aria-expanded="true" aria-controls="featuredCollapse<?= $index ?>">
                                        <?= esc($faq['question']) ?>
                                        <span class="badge bg-primary ms-2"><?= esc($faq['category']) ?></span>
                                    </button>
                                </h2>
                                <div id="featuredCollapse<?= $index ?>" class="accordion-collapse collapse show" 
                                     aria-labelledby="featuredHeading<?= $index ?>" data-bs-parent="#featuredFaqAccordion">
                                    <div class="accordion-body">
                                        <?= nl2br(esc($faq['answer'])) ?>
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