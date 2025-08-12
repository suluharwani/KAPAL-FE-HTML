<div class="container my-5">
    <h1 class="mb-5">Tim Kami</h1>
    
    <div class="row">
        <?php foreach ($team as $member): ?>
            <div class="col-md-4 mb-4">
                <div class="card h-100 shadow-sm">
                    <img src="<?= base_url('uploads/team/' . $member['photo']) ?>" class="card-img-top" alt="<?= $member['full_name'] ?>">
                    <div class="card-body">
                        <h3 class="h5 card-title"><?= $member['full_name'] ?></h3>
                        <p class="text-muted"><?= $member['position'] ?></p>
                        <p class="card-text"><?= $member['bio'] ?></p>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
</div>