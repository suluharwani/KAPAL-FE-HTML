<div class="container my-5">
    <h1 class="mb-4">Pertanyaan yang Sering Diajukan</h1>
    
    <div class="row">
        <div class="col-lg-8 mx-auto">
            <?php foreach ($faqs as $faq): ?>
                <div class="faq-item mb-4">
                    <div class="faq-question">
                        <span><?= $faq['question'] ?></span>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                    <div class="faq-answer">
                        <p><?= $faq['answer'] ?></p>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </div>
    
    <!-- Contact CTA -->
    <section class="mt-5 py-4 bg-primary text-white rounded text-center">
        <h3 class="mb-3">Masih ada pertanyaan?</h3>
        <p class="lead mb-4">Tim kami siap membantu Anda 24/7</p>
        <a href="<?= base_url('contact') ?>" class="btn btn-light btn-lg">Hubungi Kami</a>
    </section>
</div>

<script>
document.addEventListener('DOMContentLoaded', function() {
    // FAQ accordion functionality
    const faqQuestions = document.querySelectorAll('.faq-question');
    faqQuestions.forEach(question => {
        question.addEventListener('click', function() {
            const answer = this.nextElementSibling;
            const isCollapsed = this.classList.contains('collapsed');
            
            // Close all other answers
            document.querySelectorAll('.faq-answer').forEach(item => {
                if (item !== answer) {
                    item.style.display = 'none';
                    item.previousElementSibling.classList.remove('collapsed');
                }
            });
            
            // Toggle current answer
            if (isCollapsed) {
                answer.style.display = 'block';
                this.classList.remove('collapsed');
            } else {
                answer.style.display = 'none';
                this.classList.add('collapsed');
            }
        });
    });
});
</script>