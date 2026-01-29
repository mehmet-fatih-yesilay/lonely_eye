<?php
/**
 * Footer Component
 * Modern and Minimalist Design
 */
?>

<!-- Footer -->
<footer class="footer">
    <div class="container">
        <div class="row align-items-center">
            <div class="col-md-6 text-md-start text-center mb-3 mb-md-0">
                <p class="mb-0">
                    <i class="fas fa-eye text-primary"></i>
                    <strong>Lonely Eye</strong> &copy; 2026 - Tüm hakları saklıdır.
                </p>
            </div>
            <div class="col-md-6 text-md-end text-center">
                <div class="d-flex justify-content-md-end justify-content-center gap-3">
                    <a href="/lonely_eye/about.php" class="text-muted">Hakkımızda</a>
                    <a href="/lonely_eye/privacy.php" class="text-muted">Gizlilik</a>
                    <a href="/lonely_eye/terms.php" class="text-muted">Şartlar</a>
                    <a href="/lonely_eye/contact.php" class="text-muted">İletişim</a>
                </div>
            </div>
        </div>
    </div>
</footer>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

<!-- Custom JavaScript -->
<script src="/lonely_eye/assets/js/script.js"></script>

<!-- Additional Scripts -->
<script>
    // Smooth scroll for anchor links
    document.querySelectorAll('a[href^="#"]').forEach(anchor => {
        anchor.addEventListener('click', function (e) {
            const href = this.getAttribute('href');
            if (href !== '#' && document.querySelector(href)) {
                e.preventDefault();
                document.querySelector(href).scrollIntoView({
                    behavior: 'smooth'
                });
            }
        });
    });

    // Add fade-in animation to cards on scroll
    const observerOptions = {
        threshold: 0.1,
        rootMargin: '0px 0px -50px 0px'
    };

    const observer = new IntersectionObserver(function (entries) {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                entry.target.classList.add('fade-in');
                observer.unobserve(entry.target);
            }
        });
    }, observerOptions);

    document.querySelectorAll('.card').forEach(card => {
        observer.observe(card);
    });
</script>

</body>

</html>