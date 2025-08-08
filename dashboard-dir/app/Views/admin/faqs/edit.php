<?= $this->include('templates/admin_header') ?>

<div class="card">
    <div class="card-header">
        <h5 class="mb-0">Edit FAQ</h5>
    </div>
    <div class="card-body">
        <form action="<?= base_url('admin/faqs/update/' . $faq['faq_id']) ?>" method="post">
            <div class="mb-3">
                <label for="question" class="form-label">Question</label>
                <input type="text" class="form-control" id="question" name="question" value="<?= esc($faq['question']) ?>" required>
            </div>
            
            <div class="mb-3">
                <label for="answer" class="form-label">Answer</label>
                <textarea class="form-control" id="answer" name="answer" rows="5" required><?= esc($faq['answer']) ?></textarea>
            </div>
            
            <div class="mb-3">
                <label for="category" class="form-label">Category</label>
                <select class="form-select" id="category" name="category" required>
                    <option value="booking" <?= $faq['category'] == 'booking' ? 'selected' : '' ?>>Booking</option>
                    <option value="payment" <?= $faq['category'] == 'payment' ? 'selected' : '' ?>>Payment</option>
                    <option value="trip" <?= $faq['category'] == 'trip' ? 'selected' : '' ?>>Trip</option>
                    <option value="other" <?= $faq['category'] == 'other' ? 'selected' : '' ?>>Other</option>
                </select>
            </div>
            
            <div class="mb-3 form-check">
                <input type="checkbox" class="form-check-input" id="is_featured" name="is_featured" value="1" <?= $faq['is_featured'] ? 'checked' : '' ?>>
                <label class="form-check-label" for="is_featured">Featured FAQ</label>
            </div>
            
            <div class="mb-3">
                <label for="display_order" class="form-label">Display Order</label>
                <input type="number" class="form-control" id="display_order" name="display_order" value="<?= $faq['display_order'] ?>">
            </div>
            
            <button type="submit" class="btn btn-primary">Update</button>
            <a href="<?= base_url('admin/faqs') ?>" class="btn btn-secondary">Cancel</a>
        </form>
    </div>
</div>

<?= $this->include('templates/admin_footer') ?>