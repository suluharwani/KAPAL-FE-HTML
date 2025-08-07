<?= $this->extend('templates/default') ?>

<?= $this->section('content') ?>
<section class="about">
    <div class="container">
        <h1 class="section-title">Tentang Kami</h1>
        
        <div class="about-content">
            <p><?= $about['description'] ?></p>
            
            <h2>Visi & Misi</h2>
            <p><?= $about['mission'] ?></p>
            
            <h2>Tim Kami</h2>
            <div class="team-grid">
                <?php foreach ($about['team'] as $member): ?>
                <div class="team-member">
                    <img src="/images/team/<?= $member['image'] ?>" alt="<?= $member['name'] ?>">
                    <h3><?= $member['name'] ?></h3>
                    <p><?= $member['position'] ?></p>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>