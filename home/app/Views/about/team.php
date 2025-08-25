

<!-- Team Hero Section -->
<section class="team-hero py-5 bg-primary text-white">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold mb-4">Tim Kami</h1>
                <p class="lead">Kenali tim profesional yang siap melayani perjalanan Anda di Raja Ampat</p>
            </div>
        </div>
    </div>
</section>

<!-- Team Content -->
<section class="py-5">
    <div class="container">
        <div class="row mb-5">
            <div class="col-lg-8 mx-auto text-center">
                <h2 class="fw-bold mb-3">Bertemu Dengan Tim</h2>
                <p class="lead">Kami adalah tim yang berpengalaman dan berdedikasi untuk memberikan pengalaman terbaik dalam setiap perjalanan Anda</p>
            </div>
        </div>

        <!-- Team Members -->
        <div class="row">
            <?php if (!empty($teamMembers)): ?>
                <?php foreach ($teamMembers as $member): ?>
                <div class="col-md-6 col-lg-4 mb-4">
                    <div class="card team-card h-100 border-0 shadow-sm">
                        <div class="team-image-wrapper">
                            <?php if (!empty($member['image'])): ?>
                                <img src="<?= base_url('uploads/team/' . $member['image']) ?>" class="card-img-top" alt="<?= $member['name'] ?>">
                            <?php else: ?>
                                <img src="<?= base_url('images/team-placeholder.jpg') ?>" class="card-img-top" alt="Team member">
                            <?php endif; ?>
                            <div class="team-overlay">
                                <div class="team-social">
                                    <?php if (!empty($member['social_facebook'])): ?>
                                        <a href="<?= $member['social_facebook'] ?>" class="text-white me-2"><i class="fab fa-facebook-f"></i></a>
                                    <?php endif; ?>
                                    <?php if (!empty($member['social_twitter'])): ?>
                                        <a href="<?= $member['social_twitter'] ?>" class="text-white me-2"><i class="fab fa-twitter"></i></a>
                                    <?php endif; ?>
                                    <?php if (!empty($member['social_instagram'])): ?>
                                        <a href="<?= $member['social_instagram'] ?>" class="text-white me-2"><i class="fab fa-instagram"></i></a>
                                    <?php endif; ?>
                                    <?php if (!empty($member['social_linkedin'])): ?>
                                        <a href="<?= $member['social_linkedin'] ?>" class="text-white"><i class="fab fa-linkedin-in"></i></a>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="card-body text-center">
                            <h5 class="card-title fw-bold"><?= $member['name'] ?></h5>
                            <p class="card-text text-primary"><?= $member['position'] ?></p>
                            <p class="card-text small"><?= $member['bio'] ?></p>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <i class="fas fa-users fa-4x text-muted mb-3"></i>
                    <h3>Data Tim Belum Tersedia</h3>
                    <p class="text-muted">Informasi tim akan segera diupdate</p>
                </div>
            <?php endif; ?>
        </div>

        <!-- Join Team CTA -->
        <div class="row mt-5">
            <div class="col-lg-8 mx-auto">
                <div class="card bg-primary text-white text-center">
                    <div class="card-body py-5">
                        <h3 class="fw-bold mb-3">Ingin Bergabung Dengan Tim Kami?</h3>
                        <p class="lead mb-4">Kami selalu mencari talenta-talenta berbakat untuk bergabung dalam tim Raja Ampat Boat Services</p>
                        <a href="<?= base_url('contact') ?>" class="btn btn-light btn-lg">Kirim Lamaran</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
