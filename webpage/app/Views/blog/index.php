<?= $this->extend('templates/default') ?>

<?= $this->section('content') ?>
<section class="blog">
    <div class="container">
        <h1 class="section-title">Blog & Artikel</h1>
        
        <div class="blog-posts">
            <?php foreach ($posts as $post): ?>
            <article class="post-card">
                <img src="/images/<?= $post['image'] ?>" alt="<?= $post['title'] ?>">
                <div class="post-content">
                    <h2><?= $post['title'] ?></h2>
                    <p class="post-excerpt"><?= $post['excerpt'] ?></p>
                    <div class="post-meta">
                        <span><?= $post['date'] ?></span>
                        <a href="#" class="read-more">Baca Selengkapnya</a>
                    </div>
                </div>
            </article>
            <?php endforeach; ?>
        </div>
    </div>
</section>
<?= $this->endSection() ?>