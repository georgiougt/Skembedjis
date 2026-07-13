<?php
// blog-post.php
// Single Blog Post Details Template

require_once __DIR__ . '/db.php';
require_once __DIR__ . '/header.php';

$slug = $_GET['slug'] ?? '';
$post = null;

if (!empty($slug)) {
    $stmt = $db->prepare("SELECT * FROM blog_posts WHERE slug = :slug");
    $stmt->execute([':slug' => $slug]);
    $post = $stmt->fetch();
}

if (!$post) {
    // Redirect back to blog if not found
    header('Location: blog.php');
    exit;
}
?>

    <article class="single-post-sec" style="padding: 5rem 0; min-height: 50vh; background-color: var(--body-bg);">
        <div class="container" style="max-width: 800px; margin: 0 auto;">
            <a href="blog.php" style="color: var(--accent-orange); font-weight: 700; text-transform: uppercase; font-size: 0.85rem; text-decoration: none; display: inline-flex; align-items: center; gap: 0.5rem; margin-bottom: 2rem;">
                ← Back to Blog
            </a>
            
            <div class="post-meta" style="font-size: 0.9rem; color: var(--text-muted); display: flex; gap: 1rem; align-items: center; margin-bottom: 1rem;">
                <span class="post-date"><?php echo date('F j, Y', strtotime($post['created_at'])); ?></span>
                <?php if (!empty($post['badge_text'])): ?>
                    <span style="background-color: #ffcc00; color: #111111; padding: 0.2rem 0.5rem; font-size: 0.75rem; font-weight: 700; text-transform: uppercase; border-radius: 3px;"><?php echo htmlspecialchars($post['badge_text']); ?></span>
                <?php endif; ?>
            </div>
            
            <h1 style="font-size: 2.5rem; color: var(--primary-blue); font-weight: 800; line-height: 1.25; margin-bottom: 2rem;"><?php echo htmlspecialchars($post['title']); ?></h1>
            
            <div class="post-image-hero" style="width: 100%; border-radius: 8px; overflow: hidden; margin-bottom: 3rem; aspect-ratio: 16/9; background-color: #f3f4f6;">
                <img src="<?php echo htmlspecialchars($post['image_path']); ?>" alt="<?php echo htmlspecialchars($post['title']); ?>" style="width: 100%; height: 100%; object-fit: cover;">
            </div>
            
            <div class="post-body-content" style="color: #4b5563; font-size: 1.1rem; line-height: 1.8;">
                <?php echo nl2br($post['content']); ?>
            </div>
        </div>
    </article>

<?php
require_once __DIR__ . '/footer.php';
?>
