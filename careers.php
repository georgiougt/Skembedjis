<?php
// careers.php
// Careers Page

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/header.php';
?>

    <!-- Page Title Header Block -->
    <section class="page-header-block">
        <div class="container">
            <div class="divider-line centered"></div>
            <h2>Careers</h2>
            <p style="text-align: center; color: var(--text-muted); font-size: 0.95rem; margin-top: 0.5rem;">
                Join our team at Y. Skembedjis & Sons Ltd.
            </p>
        </div>
    </section>

    <!-- Careers Content Section -->
    <section style="padding: 5rem 0; background-color: var(--body-bg);">
        <div class="container" style="max-width: 800px; text-align: center;">
            <h3>We Are Hiring!</h3>
            <p style="margin: 1.5rem 0; color: var(--text-muted); font-size: 1.05rem; line-height: 1.7;">
                We are always looking for passionate Heavy Vehicle Mechanics, Forklift Operators, and Sales Representatives to join our family.
            </p>
            <p style="color: var(--text-muted); margin-bottom: 2rem; font-size: 1.05rem;">
                If you are interested in joining a dynamic team with a long-standing history of reliability since 1971, please send your CV to <strong>info@skembedjis.com</strong> or call us at <strong>70004440</strong>.
            </p>
            <a href="contact-us" class="btn btn-blue-outline">Contact Us</a>
        </div>
    </section>

<?php
require_once __DIR__ . '/footer.php';
?>
