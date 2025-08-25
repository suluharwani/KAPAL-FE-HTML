<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <nav aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item"><a href="<?= base_url() ?>">Home</a></li>
                        <li class="breadcrumb-item"><a href="<?= base_url('blog') ?>">Blog</a></li>
                        <?php if (isset($blog['category_name'])): ?>
                        <li class="breadcrumb-item"><a href="<?= base_url('blog/category/' . $blog['category_slug']) ?>"><?= $blog['category_name'] ?></a></li>
                        <?php endif; ?>
                        <li class="breadcrumb-item active" aria-current="page"><?= $blog['title'] ?></li>
                    </ol>
                </nav>
                
                <article>
                    <h1 class="display-5 fw-bold mb-3"><?= $blog['title'] ?></h1>
                    
                    <div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
                        <div class="d-flex flex-wrap align-items-center mb-2">
                            <?php if (!empty($blog['author_name'])): ?>
                            <div class="me-3 mb-2">
                                <i class="fas fa-user me-1 text-primary"></i>
                                <span><?= $blog['author_name'] ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($blog['published_at'])): ?>
                            <div class="me-3 mb-2">
                                <i class="fas fa-calendar me-1 text-primary"></i>
                                <span><?= date('d M Y', strtotime($blog['published_at'])) ?></span>
                            </div>
                            <?php endif; ?>
                            
                            <?php if (!empty($blog['category_name'])): ?>
                            <div class="mb-2">
                                <i class="fas fa-folder me-1 text-primary"></i>
                                <a href="<?= base_url('blog/category/' . $blog['category_slug']) ?>" class="text-decoration-none"><?= $blog['category_name'] ?></a>
                            </div>
                            <?php endif; ?>
                        </div>
                        
                        <div class="share-buttons mb-2">
                            <span class="me-2">Bagikan:</span>
                            <a href="https://www.facebook.com/sharer/sharer.php?u=<?= urlencode(current_url()) ?>" target="_blank" class="text-dark me-2"><i class="fab fa-facebook-f"></i></a>
                            <a href="https://twitter.com/intent/tweet?text=<?= urlencode($blog['title']) ?>&url=<?= urlencode(current_url()) ?>" target="_blank" class="text-dark me-2"><i class="fab fa-twitter"></i></a>
                            <a href="https://wa.me/?text=<?= urlencode($blog['title'] . ' ' . current_url()) ?>" target="_blank" class="text-dark"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                    
                    <?php if (!empty($blog['featured_image'])): ?>
                        <img src="<?= base_url($blog['featured_image']) ?>" class="img-fluid rounded mb-4" alt="<?= $blog['title'] ?>" style="max-height: 400px; width: 100%; object-fit: cover;">
                    <?php else: ?>
                        <img src="<?= base_url('images/blog-placeholder.jpg') ?>" class="img-fluid rounded mb-4" alt="Blog placeholder" style="max-height: 400px; width: 100%; object-fit: cover;">
                    <?php endif; ?>
                    
                    <div class="blog-content mb-5">
                        <?= $blog['content'] ?>
                    </div>
                    
                    <?php if (!empty($blog['category_name'])): ?>
                    <div class="mt-5 pt-4 border-top">
                        <h5 class="mb-3">Kategori:</h5>
                        <a href="<?= base_url('blog/category/' . $blog['category_slug']) ?>" class="badge bg-primary text-decoration-none me-1 mb-1"><?= $blog['category_name'] ?></a>
                    </div>
                    <?php endif; ?>
                </article>
                
                <!-- Related Posts - Show posts from same category -->
                <?php
                
                if (!empty($relatedPosts)): 
                ?>
                <div class="mt-5">
                    <h3 class="mb-4">Artikel Terkait</h3>
                    <div class="row">
                        <?php foreach ($relatedPosts as $related): ?>
                            <?php if ($related['blog_id'] != $blog['blog_id']): ?>
                            <div class="col-md-6 mb-3">
                                <div class="card h-100">
                                    <?php if (!empty($related['featured_image'])): ?>
                                        <img src="<?= base_url($related['featured_image']) ?>" class="card-img-top" alt="<?= $related['title'] ?>" style="height: 180px; object-fit: cover;">
                                    <?php else: ?>
                                        <img src="<?= base_url('images/blog-placeholder.jpg') ?>" class="card-img-top" alt="Blog placeholder" style="height: 180px; object-fit: cover;">
                                    <?php endif; ?>
                                    
                                    <div class="card-body">
                                        <h6 class="card-title"><?= $related['title'] ?></h6>
                                        <p class="card-text small"><?= substr(strip_tags($related['content']), 0, 100) ?>...</p>
                                        <a href="<?= base_url('blog/' . $related['slug']) ?>" class="btn btn-sm btn-outline-primary">Baca Selengkapnya</a>
                                    </div>
                                </div>
                            </div>
                            <?php endif; ?>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>
                
                <!-- Comments Section (Optional - bisa diimplementasikan later) -->
                <div class="mt-5 pt-4 border-top">
                    <h4 class="mb-3">Komentar</h4>
                    <div class="alert alert-info">
                        <i class="fas fa-info-circle me-2"></i>
                        Fitur komentar sedang dalam pengembangan. Silakan hubungi kami melalui halaman kontak untuk pertanyaan.
                    </div>
                </div>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <!-- Categories Widget -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-folder me-2"></i>Kategori</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($categories as $category): ?>
                                <li class="mb-2">
                                    <a href="<?= base_url('blog/category/' . $category['slug']) ?>" class="text-decoration-none d-flex justify-content-between align-items-center">
                                        <span><?= $category['category_name'] ?></span>
                                        <span class="badge bg-primary rounded-pill"><?= $category['post_count'] ?></span>
                                    </a>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                
                <!-- Recent Posts Widget -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Postingan Terbaru</h5>
                    </div>
                    <div class="card-body">
                        <ul class="list-unstyled mb-0">
                            <?php foreach ($recentPosts as $post): ?>
                                <li class="mb-3 pb-2 border-bottom">
                                    <a href="<?= base_url('blog/' . $post['slug']) ?>" class="text-decoration-none">
                                        <h6 class="mb-1"><?= $post['title'] ?></h6>
                                    </a>
                                    <small class="text-muted"><?= date('d M Y', strtotime($post['published_at'])) ?></small>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
                
                <!-- Newsletter Widget -->
                <div class="card mb-4">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>Berlangganan Newsletter</h5>
                    </div>
                    <div class="card-body">
                        <p class="card-text small">Dapatkan update terbaru tentang promo dan informasi wisata Raja Ampat.</p>
                        <form>
                            <div class="mb-3">
                                <input type="email" class="form-control form-control-sm" placeholder="Alamat email Anda" required>
                            </div>
                            <button type="submit" class="btn btn-primary btn-sm w-100">Berlangganan</button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>