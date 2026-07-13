<?php
// blog.php
// Recreated Blog/News Page

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/header.php';

// Fetch sections
$blog_title = get_section('blog', 'blog_title', 'Blog / News', '');
$blog_subtitle = get_section('blog', 'blog_subtitle', 'Latest Articles', '');
$blog_desc = get_section('blog', 'blog_desc', '', 'Discover our blog posts');

// Fetch articles
$posts = get_blog_posts();
?>

    <!-- Page Title Header Block -->
    <section class="page-header-block">
        <div class="container">
            <div class="divider-line centered"></div>
            <h2><?php echo htmlspecialchars($blog_title['title']); ?></h2>
            <h3 style="font-size: 1.5rem; color: var(--text-muted); font-weight: 500; margin-top: 1rem; text-align: center;">
                <?php echo htmlspecialchars($blog_subtitle['title']); ?>
            </h3>
            <p style="text-align: center; color: var(--text-muted); font-size: 0.9rem; margin-top: 0.5rem;">
                <?php echo htmlspecialchars($blog_desc['content']); ?>
            </p>
        </div>
    </section>

    <!-- Articles Grid Section -->
    <section class="blog-section">
        <div class="container">
            <div class="blog-grid">
                <?php foreach ($posts as $post): ?>
                    <article class="blog-card">
                        <div class="blog-card-img-wrapper">
                            <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>">
                            <?php if (!empty($post['badge_text'])): ?>
                                <span class="blog-card-badge"><?php echo htmlspecialchars($post['badge_text']); ?></span>
                            <?php endif; ?>
                        </div>
                        
                        <div class="blog-card-content">
                            <h3>
                                <a href="blog-post.php?slug=<?php echo urlencode($post['slug']); ?>">
                                    <?php echo htmlspecialchars($post['title']); ?>
                                </a>
                            </h3>
                            <p><?php echo htmlspecialchars($post['excerpt']); ?></p>
                        </div>
                        
                        <div class="blog-card-footer">
                            <span class="blog-card-date">
                                <?php echo date('F j, Y', strtotime($post['created_at'])); ?>
                            </span>
                            <a href="blog-post.php?slug=<?php echo urlencode($post['slug']); ?>" class="blog-read-more">
                                Read More ↗
                            </a>
                        </div>
                    </article>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

<?php
require_once __DIR__ . '/footer.php';
?>
