<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Raja Ampat Blog</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .blog-header {
            background: linear-gradient(rgba(0, 0, 0, 0.5), rgba(0, 0, 0, 0.5)), url('https://images.unsplash.com/photo-1580548259480-86f1544cf8c2?ixlib=rb-4.0.3&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            color: white;
        }
        .card-img-top {
            height: 200px;
            object-fit: cover;
        }
        .sidebar-widget {
            margin-bottom: 30px;
        }
        .recent-post-item {
            border-bottom: 1px solid #eee;
            padding: 10px 0;
        }
        .recent-post-item:last-child {
            border-bottom: none;
        }
    </style>
</head>
<body>
    <!-- Blog Header -->
    <section class="blog-header py-5 bg-light">
        <div class="container">
            <div class="row">
                <div class="col-lg-8 mx-auto text-center">
                    <h1 class="display-4 fw-bold">Raja Ampat Blog</h1>
                    <p class="lead">Discover the latest information about tourism, travel tips, and interesting stories about the Raja Ampat Islands</p>
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
                                            <img src="<?= $adminUrl . '/' . $blog['featured_image'] ?>" class="card-img-top" alt="<?= $blog['title'] ?>">
                                        <?php else: ?>
                                            <img src="<?= $adminUrl ?>/images/blog-placeholder.jpg" class="card-img-top" alt="Blog placeholder">
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
                        
                        <!-- Pagination -->
                        <?php if (isset($pager)): ?>
                            <div class="d-flex justify-content-center mt-4">
                                <?= $pager->links() ?>
                            </div>
                        <?php endif; ?>
                        
                    <?php else: ?>
                        <div class="text-center py-5">
                            <i class="fas fa-newspaper fa-4x text-muted mb-3"></i>
                            <h3>No blog articles yet</h3>
                            <p class="text-muted">Please check back later to read our latest articles.</p>
                        </div>
                    <?php endif; ?>
                </div>
                
                <!-- Sidebar -->
                <div class="col-lg-4">
                    <!-- Categories Widget -->
                    <div class="sidebar-widget card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-folder me-2"></i>Categories</h5>
                        </div>
                        <div class="card-body">
                            <ul class="list-group list-group-flush">
                                <?php if (!empty($categories)): ?>
                                    <?php foreach ($categories as $category): ?>
                                        <li class="list-group-item d-flex justify-content-between align-items-center">
                                            <a href="<?= base_url('blog/category/' . $category['slug']) ?>" class="text-decoration-none"><?= $category['category_name'] ?></a>
                                            <span class="badge bg-primary rounded-pill"><?= $category['post_count'] ?></span>
                                        </li>
                                    <?php endforeach; ?>
                                <?php else: ?>
                                    <li class="list-group-item">No categories</li>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>

                    <!-- Recent Posts Widget -->
                    <div class="sidebar-widget card shadow-sm mb-4">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-clock me-2"></i>Recent Posts</h5>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($recentPosts)): ?>
                                <?php foreach ($recentPosts as $recent): ?>
                                    <div class="recent-post-item d-flex mb-3">
                                        <?php if ($recent['featured_image']): ?>
                                            <img src="<?= $adminUrl . '/' . $recent['featured_image'] ?>" alt="<?= $recent['title'] ?>" class="flex-shrink-0 me-3" width="60" height="60" style="object-fit: cover;">
                                        <?php else: ?>
                                            <img src="<?= $adminUrl ?>/images/blog-placeholder-small.jpg" alt="Placeholder" class="flex-shrink-0 me-3" width="60" height="60" style="object-fit: cover;">
                                        <?php endif; ?>
                                        <div>
                                            <h6 class="mb-0"><a href="<?= base_url('blog/' . $recent['slug']) ?>" class="text-decoration-none"><?= $recent['title'] ?></a></h6>
                                            <small class="text-muted"><?= date('d M Y', strtotime($recent['published_at'])) ?></small>
                                        </div>
                                    </div>
                                <?php endforeach; ?>
                            <?php else: ?>
                                <p class="text-muted">No recent posts</p>
                            <?php endif; ?>
                        </div>
                    </div>

                    <!-- Newsletter Widget -->
                    <div class="sidebar-widget card shadow-sm">
                        <div class="card-header bg-primary text-white">
                            <h5 class="mb-0"><i class="fas fa-envelope me-2"></i>Newsletter</h5>
                        </div>
                        <div class="card-body">
                            <p>Subscribe to our newsletter to get the latest updates about Raja Ampat.</p>
                            <form>
                                <div class="mb-3">
                                    <input type="email" class="form-control" placeholder="Your email address" required>
                                </div>
                                <button type="submit" class="btn btn-primary w-100">Subscribe</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>