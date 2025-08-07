<?= $this->extend('templates/default') ?>

<?= $this->section('content') ?>
<section class="faq">
    <div class="container">
        <h1 class="section-title">Pertanyaan yang Sering Diajukan</h1>
        
        <div class="faq-list">
            <?php foreach ($faqs as $index => $faq): ?>
            <div class="faq-item">
                <button class="faq-question" onclick="toggleFaq(<?= $index ?>)">
                    <?= $faq['question'] ?>
                    <i class="fas fa-chevron-down"></i>
                </button>
                <div class="faq-answer" id="faq-answer-<?= $index ?>">
                    <p><?= $faq['answer'] ?></p>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
</section>

<script>
function toggleFaq(index) {
    const answer = document.getElementById(`faq-answer-${index}`);
    answer.classList.toggle('show');
}
</script>
<?= $this->endSection() ?>