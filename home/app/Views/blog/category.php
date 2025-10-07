<?= $this->extend('templates/header') ?>
<?= $this->section('content') ?>

<!-- Category Header -->
<section class="category-header py-5 bg-primary text-white">
    <div class="container">
        <div class="row">
            <div class="col-lg-8 mx-auto text-center">
                <h1 class="display-4 fw-bold"><?= $category['category_name'] ?></h1>
                <p class="lead">Latest articles in category <?= $category['category_name'] ?></p>
            </div>
        </div>
    </div>
</section>

<!-- Blog Content -->
<section class="py-5">
    <div class="container">
        <div class="row">
            <!-- Main Content -->
            <div class="col-lg-8">
                <?php if (!empty($blogs)): ?>
                    <div class="row">
                        <?php foreach ($blogs as $blog): ?>
                            <div class="col-md-6 mb-4">
                                <div class="card h-100 shadow-sm">
                                    <?php if ($blog['featured_image']): ?>
                                        <img src="<?= $adminUrl . '/' . $blog['featured_image'] ?>" class="card-img-top" alt="<?= $blog['title'] ?>" style="height: 200px; object-fit: cover;">
                                    <?php else: ?>
                                        <img src="<?= $adminUrl ?>/images/blog-placeholder.jpg" class="card-img-top" alt="Blog placeholder" style="height: 200px; object-fit: cover;">
                                    <?php endif; ?>
                                    
                                    <div class="card-body">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <span class="badge bg-primary"><?= $blog['category_name'] ?></span>
                                            <small class="text-muted"><?= date('d M Y', strtotime($blog['published_at'])) ?></small>
                                        </div>
                                        
                                        <h5 class="card-title"><?= $blog['title'] ?></h5>
                                        <p class="card-text"><?= $blog['excerpt'] ? $blog['excerpt'] : substr(strip_tags($blog['content']), 0, 100) . '...' ?></p>
                                    </div>
                                    
                                    <div class="card-footer bg-transparent">
                                        <div class="d-flex justify-content-between align-items-center">
                                            <small class="text-muted">By <?= $blog['author_name'] ?></small>
                                            <a href="<?= base_url('blog/' . $blog['slug']) ?>" class="btn btn-sm btn-outline-primary">Read More</a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php else: ?>
                    <div class="text-center py-5">
                        <i class="fas fa-newspaper fa-4x text-muted mb-3"></i>
                        <h3>No articles in this category yet</h3>
                        <p class="text-muted">Please check back later to read our latest articles.</p>
                    </div>
                <?php endif; ?>
            </div>
            
            <!-- Sidebar -->
            <div class="col-lg-4">
                <?= view_cell('App\Libraries\BlogWidgets::categories', ['categories' => $categories, 'adminUrl' => $adminUrl]) ?>
                <?= view_cell('App\Libraries\BlogWidgets::recentPosts', ['recentPosts' => $recentPosts, 'adminUrl' => $adminUrl]) ?>
                <?= view_cell('App\Libraries\BlogWidgets::newsletter') ?>
            </div>
        </div>
    </div>
</section>

<?= $this->endSection() ?>
<?= $this->include('templates/footer') ?>