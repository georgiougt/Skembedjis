<?php
// branches.php
// Recreated Branches Page

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/header.php';

// Fetch branches
$branches = get_branches();
?>

    <!-- Page Title Header Block -->
    <section class="page-header-block">
        <div class="container">
            <div class="divider-line centered"></div>
            <h2>Our Branches & Facilities</h2>
            <p style="text-align: center; color: var(--text-muted); font-size: 0.95rem; margin-top: 0.5rem;">
                Visit our physical locations and training facilities across Cyprus.
            </p>
        </div>
    </section>

    <!-- Branches Cards Section -->
    <section class="branches-section">
        <div class="container">
            <div class="branches-grid">
                <?php foreach ($branches as $b): ?>
                    <div class="branch-card">
                        <div class="branch-card-img-wrapper">
                            <img src="<?php echo htmlspecialchars($b['image_path']); ?>" alt="<?php echo htmlspecialchars($b['name']); ?>">
                        </div>
                        
                        <div class="branch-card-content">
                            <h3><?php echo htmlspecialchars($b['name']); ?></h3>
                            
                            <ul class="branch-details-list">
                                <li>
                                    <strong>Address</strong>
                                    <span><?php echo htmlspecialchars($b['address']); ?></span>
                                </li>
                                <?php if (!empty($b['postal_address'])): ?>
                                    <li>
                                        <strong>Postal Address</strong>
                                        <span><?php echo htmlspecialchars($b['postal_address']); ?></span>
                                    </li>
                                <?php endif; ?>
                                <li>
                                    <strong>Phone</strong>
                                    <span><?php echo htmlspecialchars($b['phone']); ?></span>
                                </li>
                                <li>
                                    <strong>Email</strong>
                                    <span><?php echo htmlspecialchars($b['email']); ?></span>
                                </li>
                            </ul>
                            
                            <a href="<?php echo htmlspecialchars($b['map_url']); ?>" target="_blank" rel="noopener" class="btn btn-blue-outline btn-sm">
                                View on Google Maps ↗
                            </a>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

<?php
require_once __DIR__ . '/footer.php';
?>
