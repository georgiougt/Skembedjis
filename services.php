<?php
// services.php
// Recreated Services Page

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/header.php';

// Fetch services
$services = get_services();
?>

    <!-- Page Title Header Block -->
    <section class="page-header-block">
        <div class="container">
            <div class="divider-line centered"></div>
            <h2>Our Services</h2>
            <p style="text-align: center; color: var(--text-muted); font-size: 0.95rem; margin-top: 0.5rem;">
                Providing robust, customer-first material handling equipment support and safety certifications.
            </p>
        </div>
    </section>

    <!-- Services Staggered Alternate Sections Grid -->
    <section class="services-section">
        <div class="container">
            <?php 
            $index = 0;
            foreach ($services as $s): 
                $is_even = ($index % 2 === 1);
                $index++;
            ?>
                <div class="service-row <?php echo $is_even ? 'even' : 'odd'; ?>" id="<?php echo htmlspecialchars($s['slug']); ?>">
                    <!-- Stays first in HTML to make tab focus natural; CSS row-reverse handles even row flips -->
                    <div class="service-media">
                        <img src="<?php echo htmlspecialchars($s['image_path']); ?>" alt="<?php echo htmlspecialchars($s['title']); ?>">
                    </div>
                    <div class="service-info">
                        <h3><?php echo htmlspecialchars($s['title']); ?></h3>
                        <p><?php echo htmlspecialchars($s['description']); ?></p>
                        <?php
                        $inquire_url = "contact.php?subject=" . urlencode('Inquiry about ' . $s['title']);
                        if ($s['slug'] === 'rentals') {
                            $inquire_url = "rentals.php";
                        } elseif ($s['slug'] === 'sell-machine') {
                            $inquire_url = "sell-machine.php";
                        } elseif ($s['slug'] === 'repairs-services') {
                            $inquire_url = "repairs-services.php";
                        } elseif ($s['slug'] === 'operator-training') {
                            $inquire_url = "operator-training.php";
                        }
                        ?>
                        <a href="<?php echo htmlspecialchars($inquire_url); ?>" class="btn btn-blue-outline btn-sm">
                            Inquire Now
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    </section>

<?php
require_once __DIR__ . '/footer.php';
?>
