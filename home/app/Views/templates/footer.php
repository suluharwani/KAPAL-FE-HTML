<footer class="bg-dark text-white pt-5 pb-3">
    <div class="container">
        <div class="row">
            <!-- About -->
            <div class="col-lg-4 mb-4">
                <h5 class="mb-3"><?= esc($settings['site_name'] ?? '') ?></h5>
                <p><?= esc($settings['footer_text'] ?? '') ?></p>
                <div class="social-icons">
                    <!-- Karena tidak ada data medsos di settings, biarkan kosong -->
                </div>
            </div>

            <!-- Quick Links -->
            <div class="col-lg-2 col-md-6 mb-4">
                <h5 class="mb-3">Quick Links</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><a href="<?= base_url() ?>" class="text-white">Home</a></li>
                    <li class="mb-2"><a href="<?= base_url('about') ?>" class="text-white">About Us</a></li>
                    <li class="mb-2"><a href="<?= base_url('blog') ?>" class="text-white">Blog</a></li>
                    <li class="mb-2"><a href="<?= base_url('contact') ?>" class="text-white">Contact</a></li>
                    <li class="mb-2"><a href="<?= base_url('faq') ?>" class="text-white">FAQ</a></li>
                </ul>
            </div>

            <!-- Contact -->
            <div class="col-lg-3 col-md-6 mb-4">
                <h5 class="mb-3">Contact Us</h5>
                <ul class="list-unstyled">
                    <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i> <?= esc($settings['site_address'] ?? '') ?></li>
                    <li class="mb-2"><i class="fas fa-phone-alt me-2"></i> <?= esc($settings['site_phone'] ?? '') ?></li>
                    <li class="mb-2"><i class="fas fa-envelope me-2"></i> <?= esc($settings['site_email'] ?? '') ?></li>
                </ul>
            </div>

            <!-- Opening Hours -->
            <div class="col-lg-3 mb-4">
                <h5 class="mb-3">Operating Hours</h5>
                <ul class="list-unstyled">
                    <li class="mb-2">Monday - Friday: 08:00 - 17:00</li>
                    <li class="mb-2">Saturday: 08:00 - 15:00</li>
                    <li>Sunday: Closed</li>
                </ul>
            </div>
        </div>

        <hr class="my-4 bg-secondary">

        <div class="row">
            <div class="col-md-6 text-center text-md-start">
                <p class="mb-0">&copy; <?= date('Y') ?> <?= esc($settings['site_name'] ?? '') ?>. All rights reserved.</p>
            </div>
            <div class="col-md-6 text-center text-md-end">
                <p class="mb-0">Designed with <i class="fas fa-heart text-danger"></i></p>
            </div>
        </div>
    </div>
</footer>



<!-- WhatsApp Floating Button -->
<a href="https://wa.me/6281234567890" class="whatsapp-float" target="_blank">
    <i class="fab fa-whatsapp whatsapp-float-icon"></i>
</a>

<!-- JavaScript Libraries -->
<script src="<?= base_url('js/bootstrap.bundle.min.js') ?>"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/lightbox2/2.11.3/js/lightbox.min.js"></script>
<script src="<?= base_url('js/script.js') ?>"></script>
</body>
</html>