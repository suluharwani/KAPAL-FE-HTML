<?= $this->extend('templates/default') ?>

<?= $this->section('content') ?>
<section class="blog-detail">
    <div class="container">
        <article>
            <h1><?= $post['title'] ?></h1>
            <div class="post-meta">
                <span>Ditulis oleh <?= $post['author'] ?></span>
                <span><?= $post['date'] ?></span>
            </div>
            
            <img src="/images/<?= $post['image'] ?>" alt="<?= $post['title'] ?>" class="featured-image">
            
            <div class="post-content">
                <?= $post['content'] ?>
            </div>
        </article>
    </div>
</section>
<?= $this->endSection() ?>