<?= $this->extend('templates/default') ?>

<?= $this->section('content') ?>
<section class="contact">
    <div class="container">
        <h1 class="section-title">Hubungi Kami</h1>
        
        <div class="contact-container">
            <div class="contact-info">
                <h2>Informasi Kontak</h2>
                <ul>
                    <li><i class="fas fa-map-marker-alt"></i> <?= $contact_info['address'] ?></li>
                    <li><i class="fas fa-phone"></i> <?= $contact_info['phone'] ?></li>
                    <li><i class="fas fa-envelope"></i> <?= $contact_info['email'] ?></li>
                    <li><i class="fas fa-clock"></i> <?= $contact_info['hours'] ?></li>
                </ul>
                
                <div class="social-media">
                    <a href="#"><i class="fab fa-facebook"></i></a>
                    <a href="#"><i class="fab fa-instagram"></i></a>
                    <a href="#"><i class="fab fa-whatsapp"></i></a>
                </div>
            </div>
            
            <div class="contact-form">
                <h2>Kirim Pesan</h2>
                <?php if (session()->getFlashdata('success')): ?>
                <div class="alert alert-success">
                    <?= session()->getFlashdata('success') ?>
                </div>
                <?php endif; ?>
                
                <form action="<?= base_url('/kontak/submit') ?>" method="post">
                    <?= csrf_field() ?>
                    
                    <div class="form-group">
                        <label for="name">Nama Lengkap</label>
                        <input type="text" id="name" name="name" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" id="email" name="email" class="form-control" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="message">Pesan</label>
                        <textarea id="message" name="message" rows="5" class="form-control" required></textarea>
                    </div>
                    
                    <button type="submit" class="btn btn-primary">Kirim Pesan</button>
                </form>
            </div>
        </div>
    </div>
</section>
<?= $this->endSection() ?>