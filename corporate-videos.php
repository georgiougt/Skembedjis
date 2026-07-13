<?php
// corporate-videos.php
// Recreated Corporate Videos Page

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/header.php';

// Fetch corporate videos
$videos = get_corporate_videos();
?>

    <!-- Page Title Header Block -->
    <section class="page-header-block">
        <div class="container">
            <div class="divider-line centered"></div>
            <h2>Corporate Videos</h2>
            <p style="text-align: center; color: var(--text-muted); font-size: 0.95rem; margin-top: 0.5rem;">
                Watch our facility operations and machinery service centers in action.
            </p>
        </div>
    </section>

    <!-- Videos Grid Section -->
    <section class="videos-section">
        <div class="container">
            <div class="videos-grid">
                <?php foreach ($videos as $v): ?>
                    <div class="video-card">
                        <div class="video-wrapper">
                            <video controls preload="metadata" poster="<?php echo htmlspecialchars($v['thumbnail_url'] ?: ''); ?>">
                                <source src="<?php echo htmlspecialchars($v['video_url']); ?>" type="video/mp4">
                                Your browser does not support the video tag.
                            </video>
                        </div>
                        <div class="video-info">
                            <h4><?php echo htmlspecialchars($v['title']); ?></h4>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

<?php
require_once __DIR__ . '/footer.php';
?>
