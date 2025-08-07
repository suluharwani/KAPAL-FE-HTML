<?= $this->extend('templates/default') ?>

<?= $this->section('content') ?>
<section class="destinations">
    <div class="container">
        <h1 class="section-title">Destinasi Wisata Raja Ampat</h1>
        
        <div class="destination-grid">
            <?php foreach ($destinations as $destination): ?>
            <div class="destination-card">
                <img src="/images/<?= $destination['image'] ?>" alt="<?= $destination['name'] ?>">
                <div class="destination-info">
                    <h3><?= $destination['name'] ?></h3>
                    <p><?= $destination['description'] ?></p>
                    <a href="#" class="btn btn-outline">Lihat Detail</a>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?= $this->endSection() ?>