<?php
// admin/index.php
// Main admin dashboard

require_once __DIR__ . '/auth_check.php';
check_login();

// Handle AJAX Sync Requests
if (isset($_GET['action']) && $_GET['action'] === 'ajax_sync') {
    header('Content-Type: application/json');
    require_once __DIR__ . '/../sync_products.php';
    
    $step = $_GET['step'] ?? 'fetch';
    $source = $_GET['source'] ?? 'json';
    
    if ($step === 'fetch') {
        if ($source === 'soap') {
            $res = pull_soap_and_save_json();
        } else {
            $res = ['success' => true, 'message' => 'Local JSON source ready.'];
            
            // Get total count of items in local json
            $jsonPath = __DIR__ . '/../items.json';
            if (file_exists($jsonPath)) {
                $all = json_decode(file_get_contents($jsonPath), true);
                if (is_array($all)) {
                    $res['total_items'] = count($all);
                } else {
                    $res = ['success' => false, 'message' => 'Failed to parse local items.json.'];
                }
            } else {
                $res = ['success' => false, 'message' => 'items.json file not found.'];
            }
        }
        echo json_encode($res);
        exit;
    }
    
    if ($step === 'sync') {
        $offset = isset($_GET['offset']) ? (int)$_GET['offset'] : 0;
        $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 30;
        
        $res = run_products_sync(null, $offset, $limit);
        echo json_encode($res);
        exit;
    }
    
    echo json_encode(['success' => false, 'message' => 'Invalid sync step.']);
    exit;
}

$tab = $_GET['tab'] ?? 'dashboard';
$success_msg = $_GET['success'] ?? '';
$error_msg = $_GET['error'] ?? '';

// Handle POST actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = $_POST['action'] ?? '';

    if ($action === 'update_settings') {
        foreach ($_POST['settings'] as $key => $value) {
            set_setting($key, $value);
        }
        $success_msg = 'Site settings updated successfully!';
    }
    
    elseif ($action === 'sync_catalog') {
        require_once __DIR__ . '/../sync_products.php';
        $source = $_POST['sync_source'] ?? 'json';
        if ($source === 'soap') {
            $res = pull_soap_and_sync();
        } else {
            $res = run_products_sync();
        }
        if ($res['success']) {
            $success_msg = $res['message'];
        } else {
            $error_msg = $res['message'];
        }
    }
    
    elseif ($action === 'update_section') {
        $id = (int)($_POST['id'] ?? 0);
        $page = trim($_POST['page'] ?? '');
        $section_key = trim($_POST['section_key'] ?? '');
        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');

        if (!empty($page) && !empty($section_key)) {
            update_section($page, $section_key, $title, $content);
            $success_msg = "Section '{$section_key}' updated successfully!";
        } else {
            $error_msg = 'Page and section key cannot be empty.';
        }
    }

    elseif ($action === 'update_image') {
        $image_key = trim($_POST['image_key'] ?? '');
        $alt_text = trim($_POST['alt_text'] ?? '');
        
        // Handle file upload if present
        $image_path = '';
        if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['image_file']['tmp_name'];
            $file_name = basename($_FILES['image_file']['name']);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            
            $allowed_exts = ['jpg', 'jpeg', 'png', 'svg', 'webp', 'gif'];
            if (in_array($file_ext, $allowed_exts)) {
                $upload_dir = __DIR__ . '/../uploads/';
                if (!is_dir($upload_dir)) {
                    mkdir($upload_dir, 0755, true);
                }
                
                $new_filename = uniqid('img_', true) . '.' . $file_ext;
                $target_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($file_tmp, $target_path)) {
                    $image_path = 'uploads/' . $new_filename;
                } else {
                    $error_msg = 'Failed to move uploaded file.';
                }
            } else {
                $error_msg = 'Invalid file type. Allowed: ' . implode(', ', $allowed_exts);
            }
        }

        if (empty($error_msg) && !empty($image_key)) {
            // Check if existing
            $stmtCheck = $db->prepare("SELECT id, image_path FROM site_images WHERE image_key = :key");
            $stmtCheck->execute([':key' => $image_key]);
            $existing = $stmtCheck->fetch();

            if ($existing) {
                if (!empty($image_path)) {
                    // Delete old file if it exists and is an uploaded file
                    if (!empty($existing['image_path']) && strpos($existing['image_path'], 'placeholder.php') === false && file_exists(__DIR__ . '/../' . $existing['image_path'])) {
                        @unlink(__DIR__ . '/../' . $existing['image_path']);
                    }
                    $stmtUpdate = $db->prepare("UPDATE site_images SET image_path = :path, alt_text = :alt WHERE image_key = :key");
                    $stmtUpdate->execute([':path' => $image_path, ':alt' => $alt_text, ':key' => $image_key]);
                } else {
                    $stmtUpdate = $db->prepare("UPDATE site_images SET alt_text = :alt WHERE image_key = :key");
                    $stmtUpdate->execute([':alt' => $alt_text, ':key' => $image_key]);
                }
            } else {
                if (empty($image_path)) {
                    $image_path = "placeholder.php?text=" . urlencode($alt_text);
                }
                $stmtInsert = $db->prepare("INSERT INTO site_images (image_key, image_path, alt_text) VALUES (:key, :path, :alt)");
                $stmtInsert->execute([':key' => $image_key, ':path' => $image_path, ':alt' => $alt_text]);
            }
            $success_msg = 'Image config updated successfully!';
        }
    }
    
    elseif ($action === 'change_password') {
        $old_pass = $_POST['old_password'] ?? '';
        $new_pass = $_POST['new_password'] ?? '';
        $confirm_pass = $_POST['confirm_password'] ?? '';

        if (!empty($old_pass) && !empty($new_pass) && !empty($confirm_pass)) {
            if ($new_pass === $confirm_pass) {
                // Fetch user
                $stmt = $db->prepare("SELECT * FROM users WHERE username = :username");
                $stmt->execute([':username' => $_SESSION['admin_username']]);
                $user = $stmt->fetch();

                if ($user && password_verify($old_pass, $user['password'])) {
                    $new_hash = password_hash($new_pass, PASSWORD_DEFAULT);
                    $stmtUpdate = $db->prepare("UPDATE users SET password = :password WHERE id = :id");
                    $stmtUpdate->execute([':password' => $new_hash, ':id' => $user['id']]);
                    $success_msg = 'Password changed successfully!';
                } else {
                    $error_msg = 'Incorrect current password.';
                }
            } else {
                $error_msg = 'New passwords do not match.';
            }
        } else {
            $error_msg = 'Please fill in all password fields.';
        }
    }
    
    elseif ($action === 'add_faq') {
        $question = trim($_POST['question'] ?? '');
        $answer = trim($_POST['answer'] ?? '');
        $sort_order = (int)($_POST['sort_order'] ?? 0);

        if (!empty($question) && !empty($answer)) {
            $stmt = $db->prepare("INSERT INTO faqs (question, answer, sort_order) VALUES (:q, :a, :order)");
            $stmt->execute([':q' => $question, ':a' => $answer, ':order' => $sort_order]);
            $success_msg = 'FAQ added successfully!';
        } else {
            $error_msg = 'Question and answer cannot be empty.';
        }
    } 
    
    elseif ($action === 'update_faq') {
        $id = (int)($_POST['id'] ?? 0);
        $question = trim($_POST['question'] ?? '');
        $answer = trim($_POST['answer'] ?? '');
        $sort_order = (int)($_POST['sort_order'] ?? 0);

        if ($id > 0 && !empty($question) && !empty($answer)) {
            $stmt = $db->prepare("UPDATE faqs SET question = :q, answer = :a, sort_order = :order WHERE id = :id");
            $stmt->execute([':q' => $question, ':a' => $answer, ':order' => $sort_order, ':id' => $id]);
            $success_msg = 'FAQ updated successfully!';
        } else {
            $error_msg = 'Question and answer cannot be empty.';
        }
    } 
    
    elseif ($action === 'delete_faq') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $db->prepare("DELETE FROM faqs WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $success_msg = 'FAQ deleted successfully!';
        }
    }

    elseif ($action === 'add_blog') {
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $badge_text = trim($_POST['badge_text'] ?? '');
        $created_at = trim($_POST['created_at'] ?? '');

        // Handle image upload
        $image_path = 'placeholder.php?text=Blog+Article&w=600&h=400';
        if (isset($_FILES['blog_image']) && $_FILES['blog_image']['error'] === UPLOAD_ERR_OK) {
            $file_tmp = $_FILES['blog_image']['tmp_name'];
            $file_name = basename($_FILES['blog_image']['name']);
            $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

            if (in_array($file_ext, $allowed)) {
                $upload_dir = __DIR__ . '/../uploads/';
                if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                
                $new_name = uniqid('blog_', true) . '.' . $file_ext;
                if (move_uploaded_file($file_tmp, $upload_dir . $new_name)) {
                    $image_path = 'uploads/' . $new_name;
                }
            }
        }

        if (!empty($title) && !empty($slug)) {
            try {
                $stmt = $db->prepare("INSERT INTO blog_posts (title, slug, excerpt, content, image_path, badge_text, created_at) VALUES (:title, :slug, :excerpt, :content, :img, :badge, :date)");
                $stmt->execute([
                    ':title' => $title,
                    ':slug' => $slug,
                    ':excerpt' => $excerpt,
                    ':content' => $content,
                    ':img' => $image_path,
                    ':badge' => $badge_text,
                    ':date' => empty($created_at) ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s', strtotime($created_at))
                ]);
                $success_msg = 'Blog article published successfully!';
            } catch (PDOException $e) {
                $error_msg = 'Error publishing article: Slug might already exist.';
            }
        } else {
            $error_msg = 'Title and slug cannot be empty.';
        }
    }

    elseif ($action === 'update_blog') {
        $id = (int)($_POST['id'] ?? 0);
        $title = trim($_POST['title'] ?? '');
        $slug = trim($_POST['slug'] ?? '');
        $excerpt = trim($_POST['excerpt'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $badge_text = trim($_POST['badge_text'] ?? '');
        $created_at = trim($_POST['created_at'] ?? '');

        if ($id > 0 && !empty($title) && !empty($slug)) {
            // Check existing post image to preserve if no upload
            $stmtCheck = $db->prepare("SELECT image_path FROM blog_posts WHERE id = :id");
            $stmtCheck->execute([':id' => $id]);
            $existing_img = $stmtCheck->fetchColumn();
            
            $image_path = $existing_img;
            if (isset($_FILES['blog_image']) && $_FILES['blog_image']['error'] === UPLOAD_ERR_OK) {
                $file_tmp = $_FILES['blog_image']['tmp_name'];
                $file_name = basename($_FILES['blog_image']['name']);
                $file_ext = strtolower(pathinfo($file_name, PATHINFO_EXTENSION));
                $allowed = ['jpg', 'jpeg', 'png', 'webp', 'gif'];

                if (in_array($file_ext, $allowed)) {
                    $upload_dir = __DIR__ . '/../uploads/';
                    if (!is_dir($upload_dir)) mkdir($upload_dir, 0755, true);
                    
                    $new_name = uniqid('blog_', true) . '.' . $file_ext;
                    if (move_uploaded_file($file_tmp, $upload_dir . $new_name)) {
                        // Delete old file
                        if (!empty($existing_img) && strpos($existing_img, 'placeholder.php') === false && file_exists(__DIR__ . '/../' . $existing_img)) {
                            @unlink(__DIR__ . '/../' . $existing_img);
                        }
                        $image_path = 'uploads/' . $new_name;
                    }
                }
            }

            try {
                $stmt = $db->prepare("UPDATE blog_posts SET title = :title, slug = :slug, excerpt = :excerpt, content = :content, image_path = :img, badge_text = :badge, created_at = :date WHERE id = :id");
                $stmt->execute([
                    ':title' => $title,
                    ':slug' => $slug,
                    ':excerpt' => $excerpt,
                    ':content' => $content,
                    ':img' => $image_path,
                    ':badge' => $badge_text,
                    ':date' => empty($created_at) ? date('Y-m-d H:i:s') : date('Y-m-d H:i:s', strtotime($created_at)),
                    ':id' => $id
                ]);
                $success_msg = 'Blog article updated successfully!';
            } catch (PDOException $e) {
                $error_msg = 'Error updating article: Slug might already exist.';
            }
        } else {
            $error_msg = 'Invalid article identifiers.';
        }
    }

    elseif ($action === 'delete_blog') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            // Delete upload image file
            $stmtCheck = $db->prepare("SELECT image_path FROM blog_posts WHERE id = :id");
            $stmtCheck->execute([':id' => $id]);
            $img = $stmtCheck->fetchColumn();

            if (!empty($img) && strpos($img, 'placeholder.php') === false && file_exists(__DIR__ . '/../' . $img)) {
                @unlink(__DIR__ . '/../' . $img);
            }

            $stmt = $db->prepare("DELETE FROM blog_posts WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $success_msg = 'Blog article deleted successfully!';
        }
    }

    elseif ($action === 'delete_rental') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $db->prepare("DELETE FROM rental_requests WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $success_msg = 'Rental request deleted successfully!';
        }
    }

    elseif ($action === 'delete_sell') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmtCheck = $db->prepare("SELECT photo_path FROM sell_machine_requests WHERE id = :id");
            $stmtCheck->execute([':id' => $id]);
            $photo = $stmtCheck->fetchColumn();

            if (!empty($photo) && file_exists(__DIR__ . '/../' . $photo)) {
                @unlink(__DIR__ . '/../' . $photo);
            }

            $stmt = $db->prepare("DELETE FROM sell_machine_requests WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $success_msg = 'Sale request deleted successfully!';
        }
    }

    elseif ($action === 'delete_part_request') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $db->prepare("DELETE FROM part_requests WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $success_msg = 'Part request deleted successfully!';
        }
    }

    elseif ($action === 'delete_machine_request') {
        $id = (int)($_POST['id'] ?? 0);
        if ($id > 0) {
            $stmt = $db->prepare("DELETE FROM machine_requests WHERE id = :id");
            $stmt->execute([':id' => $id]);
            $success_msg = 'Machine inquiry request deleted successfully!';
        }
    }
}

// Fetch stats for dashboard overview
$total_sections = $db->query("SELECT COUNT(*) FROM content_sections")->fetchColumn();
$total_images = $db->query("SELECT COUNT(*) FROM site_images")->fetchColumn();
$total_faqs = $db->query("SELECT COUNT(*) FROM faqs")->fetchColumn();
$total_posts = $db->query("SELECT COUNT(*) FROM blog_posts")->fetchColumn();
$total_rentals = $db->query("SELECT COUNT(*) FROM rental_requests")->fetchColumn();
$total_sells = $db->query("SELECT COUNT(*) FROM sell_machine_requests")->fetchColumn();
$total_parts = $db->query("SELECT COUNT(*) FROM part_requests")->fetchColumn();
$total_machine_requests = $db->query("SELECT COUNT(*) FROM machine_requests")->fetchColumn();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, nofollow">
    <title>Admin Control Panel - Portfolio Dashboard</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="admin-style.css">
    <style>
        .sidebar-title {
            font-size: 1.25rem;
            font-weight: 800;
            background: linear-gradient(135deg, var(--primary), var(--secondary));
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
        }
        .user-tag {
            font-size: 0.85rem;
            color: var(--text-muted);
            margin-top: auto;
            padding: 0.5rem;
            border-top: 1px solid var(--border);
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .stat-card {
            display: flex;
            align-items: center;
            justify-content: space-between;
        }
        .stat-val {
            font-size: 2rem;
            font-weight: 800;
            color: var(--primary);
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 1rem;
        }
        @media (max-width: 768px) {
            .admin-layout {
                flex-direction: column;
            }
            .admin-sidebar {
                width: 100%;
                border-right: none;
                border-bottom: 1px solid var(--border);
            }
            .form-row {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="admin-layout">
        <!-- Sidebar -->
        <aside class="admin-sidebar">
            <div class="admin-logo-area">
                <span class="sidebar-title">PORTFOLIO CMS</span>
            </div>
            
            <nav class="admin-nav">
                <a href="?tab=dashboard" class="admin-nav-item <?php echo $tab === 'dashboard' ? 'active' : ''; ?>">
                    Dashboard
                </a>
                <a href="?tab=sections" class="admin-nav-item <?php echo $tab === 'sections' ? 'active' : ''; ?>">
                    Content Sections
                </a>
                <a href="?tab=images" class="admin-nav-item <?php echo $tab === 'images' ? 'active' : ''; ?>">
                    Manage Images
                </a>
                <a href="?tab=faqs" class="admin-nav-item <?php echo $tab === 'faqs' ? 'active' : ''; ?>">
                    Manage FAQs
                </a>
                <a href="?tab=blog" class="admin-nav-item <?php echo $tab === 'blog' ? 'active' : ''; ?>">
                    Manage Blog
                </a>
                <a href="?tab=rentals" class="admin-nav-item <?php echo $tab === 'rentals' ? 'active' : ''; ?>">
                    Rental Requests
                </a>
                <a href="?tab=sell_machines" class="admin-nav-item <?php echo $tab === 'sell_machines' ? 'active' : ''; ?>">
                    Sell Inquiries
                </a>
                <a href="?tab=part_requests" class="admin-nav-item <?php echo $tab === 'part_requests' ? 'active' : ''; ?>">
                    Part Requests
                </a>
                <a href="?tab=machine_requests" class="admin-nav-item <?php echo $tab === 'machine_requests' ? 'active' : ''; ?>">
                    Machine Inquiries
                </a>
                <a href="?tab=settings" class="admin-nav-item <?php echo $tab === 'settings' ? 'active' : ''; ?>">
                    Site Settings
                </a>
                <a href="?tab=sync_history" class="admin-nav-item <?php echo $tab === 'sync_history' ? 'active' : ''; ?>">
                    Sync History Logs
                </a>
                <a href="?tab=security" class="admin-nav-item <?php echo $tab === 'security' ? 'active' : ''; ?>">
                    Security
                </a>
                <a href="../index.php" target="_blank" class="admin-nav-item">
                    View Live Site ↗
                </a>
            </nav>
            
            <div class="user-tag">
                <span>Logged in as: <strong><?php echo htmlspecialchars($_SESSION['admin_username']); ?></strong></span>
                <a href="logout.php" style="color: var(--danger); font-weight: 600;">Logout</a>
            </div>
        </aside>

        <!-- Main Content Area -->
        <main class="admin-content">
            <?php
            // Query the latest sync log
            $latestSync = $db->query("SELECT status, sync_time, message FROM sync_logs ORDER BY id DESC LIMIT 1")->fetch();
            if ($latestSync && $latestSync['status'] === 'Failed'):
            ?>
                <div style="background-color: #fee2e2; border-left: 5px solid #ef4444; color: #991b1b; padding: 1rem 1.5rem; margin-bottom: 2rem; border-radius: 6px; display: flex; align-items: center; justify-content: space-between; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                    <div style="display: flex; align-items: center; gap: 0.75rem;">
                        <span style="font-size: 1.5rem;">⚠️</span>
                        <div>
                            <strong style="font-weight: 700;">1C Stock Sync Failed!</strong> 
                            <span style="font-size: 0.9rem; display: block; margin-top: 0.15rem;">
                                The last sync attempt on <code style="background: rgba(0,0,0,0.05); padding: 0.1rem 0.3rem; border-radius: 4px;"><?php echo htmlspecialchars($latestSync['sync_time']); ?></code> failed. Reason: <em><?php echo htmlspecialchars($latestSync['message']); ?></em>
                            </span>
                        </div>
                    </div>
                    <a href="?tab=sync_history" style="background: #ef4444; color: #ffffff; padding: 0.45rem 1rem; border-radius: 4px; text-decoration: none; font-size: 0.85rem; font-weight: 700; transition: opacity 0.15s;" onmouseover="this.style.opacity=0.85" onmouseout="this.style.opacity=1">
                        View Log
                    </a>
                </div>
            <?php endif; ?>

            <div class="admin-header">
                <h2><?php echo ucfirst($tab); ?> Panel</h2>
                <p>Manage and monitor website configurations</p>
            </div>

            <!-- Alerts -->
            <?php if (!empty($success_msg)): ?>
                <div class="alert alert-success"><?php echo $success_msg; ?></div>
            <?php endif; ?>
            <?php if (!empty($error_msg)): ?>
                <div class="alert alert-danger"><?php echo $error_msg; ?></div>
            <?php endif; ?>

            <!-- Dashboard Overview Tab -->
            <?php if ($tab === 'dashboard'): ?>
                <div class="grid grid-cols-4" style="grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));">
                    <div class="card stat-card">
                        <div>
                            <h4>Dynamic Sections</h4>
                            <p>Text blocks managed</p>
                        </div>
                        <span class="stat-val"><?php echo $total_sections; ?></span>
                    </div>
                    <div class="card stat-card">
                        <div>
                            <h4>Website Images</h4>
                            <p>Image items configured</p>
                        </div>
                        <span class="stat-val"><?php echo $total_images; ?></span>
                    </div>
                    <div class="card stat-card">
                        <div>
                            <h4>FAQ Accordions</h4>
                            <p>Questions online</p>
                        </div>
                        <span class="stat-val"><?php echo $total_faqs; ?></span>
                    </div>
                    <div class="card stat-card">
                        <div>
                            <h4>Blog Articles</h4>
                            <p>News posts online</p>
                        </div>
                        <span class="stat-val"><?php echo $total_posts; ?></span>
                    </div>
                    <div class="card stat-card">
                        <div>
                            <h4>Rental Requests</h4>
                            <p>Forms received</p>
                        </div>
                        <span class="stat-val"><?php echo $total_rentals; ?></span>
                    </div>
                    <div class="card stat-card">
                        <div>
                            <h4>Sell Inquiries</h4>
                            <p>Forms received</p>
                        </div>
                        <span class="stat-val"><?php echo $total_sells; ?></span>
                    </div>
                    <div class="card stat-card">
                        <div>
                            <h4>Part Requests</h4>
                            <p>Forms received</p>
                        </div>
                        <span class="stat-val"><?php echo $total_parts; ?></span>
                    </div>
                    <div class="card stat-card">
                        <div>
                            <h4>Machine Inquiries</h4>
                            <p>Forms received</p>
                        </div>
                        <span class="stat-val"><?php echo $total_machine_requests; ?></span>
                    </div>
                </div>
                
                <div class="card" style="margin-top: 2rem;">
                    <h3>Welcome to the Admin Dashboard!</h3>
                    <p style="margin-top: 1rem;">Use the menu navigation on the left to configure page contents, upload replacement images, adjust global settings like social links or contact emails, and secure your credentials.</p>
                    
                    <div style="margin-top: 2rem; border-top: 1px solid var(--border-gray); padding-top: 1.5rem;">
                        <h4 style="font-weight: 700; font-size: 1.1rem; color: var(--primary-blue); margin-bottom: 0.5rem;">Inventory Synchronization Utility</h4>
                        <p style="color: var(--text-muted); font-size: 0.9rem; margin-bottom: 1.25rem;">Synchronize your SQLite database with your 1C inventory. You can pull live data from the 1C SOAP web service or use a manual <code>items.json</code> file.</p>
                        
                        <div style="display: flex; gap: 1.5rem; flex-wrap: wrap;">
                            <!-- 1C SOAP Sync -->
                            <button type="button" onclick="startChunkedSync('soap')" class="hz-filter-btn" style="padding: 0.65rem 1.5rem; background: #eab308; color: #000000; font-weight: 700; border: none; border-radius: 4px; cursor: pointer; transition: background 0.2s;">
                                ⚡ Pull & Sync from 1C SOAP (Chunked)
                            </button>

                            <!-- Local JSON Sync -->
                            <button type="button" onclick="startChunkedSync('json')" class="hz-filter-btn" style="padding: 0.65rem 1.5rem; background: var(--primary-blue); color: #ffffff; font-weight: 700; border: none; border-radius: 4px; cursor: pointer; transition: background 0.2s;">
                                Sync from Local items.json (Chunked)
                            </button>
                        </div>

                        <!-- Progress Bar UI -->
                        <div id="syncProgressContainer" style="display: none; margin-top: 1.5rem; background: #f8fafc; border: 1px solid #e2e8f0; padding: 1.5rem; border-radius: 6px; box-shadow: 0 4px 6px -1px rgba(0,0,0,0.05);">
                            <h5 id="syncProgressTitle" style="margin: 0 0 0.5rem; font-weight: 700; font-size: 0.95rem; color: var(--text);">Synchronizing Inventory...</h5>
                            <p id="syncStatusText" style="margin: 0 0 1rem; font-size: 0.85rem; color: var(--text-muted);">Preparing data...</p>
                            
                            <div style="background: #e2e8f0; height: 12px; border-radius: 6px; overflow: hidden; position: relative;">
                                <div id="syncProgressBar" style="width: 0%; height: 100%; background: var(--primary-blue); transition: width 0.3s ease;"></div>
                            </div>
                            
                            <div style="display: flex; justify-content: space-between; align-items: center; margin-top: 0.5rem;">
                                <span id="syncProgressLabel" style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted);">0%</span>
                                <span id="syncProgressCount" style="font-size: 0.8rem; font-weight: 600; color: var(--text-muted);">0 / 0 items</span>
                            </div>
                        </div>

                        <!-- JS Controller -->
                        <script>
                        async function startChunkedSync(source) {
                            const progressContainer = document.getElementById('syncProgressContainer');
                            const progressBar = document.getElementById('syncProgressBar');
                            const statusText = document.getElementById('syncStatusText');
                            const progressLabel = document.getElementById('syncProgressLabel');
                            const progressCount = document.getElementById('syncProgressCount');
                            
                            progressContainer.style.display = 'block';
                            progressBar.style.width = '0%';
                            progressBar.style.background = 'var(--primary-blue)';
                            statusText.style.color = 'var(--text-muted)';
                            progressLabel.innerText = '0%';
                            progressCount.innerText = 'Initializing...';
                            
                            try {
                                // Step 1: Fetch SOAP data or validate local JSON and get item count
                                statusText.innerText = source === 'soap' ? 'Connecting to 1C SOAP service and fetching catalog items...' : 'Validating local items.json file...';
                                
                                const fetchUrl = `index.php?action=ajax_sync&step=fetch&source=${source}`;
                                const fetchResponse = await fetch(fetchUrl);
                                const fetchData = await fetchResponse.json();
                                
                                if (!fetchData.success) {
                                    alert('Error during initial fetch: ' + fetchData.message);
                                    progressContainer.style.display = 'none';
                                    return;
                                }
                                
                                const totalItems = parseInt(fetchData.total_items) || 0;
                                if (totalItems === 0) {
                                    alert('No items found to synchronize.');
                                    progressContainer.style.display = 'none';
                                    return;
                                }
                                
                                statusText.innerText = `Found ${totalItems} items. Starting database and WebP image synchronization...`;
                                progressCount.innerText = `0 / ${totalItems} items`;
                                
                                // Step 2: Loop sync in chunks of 30
                                const limit = 30;
                                let offset = 0;
                                
                                while (offset < totalItems) {
                                    statusText.innerText = `Processing items ${offset + 1} to ${Math.min(offset + limit, totalItems)} of ${totalItems}...`;
                                    
                                    const syncUrl = `index.php?action=ajax_sync&step=sync&offset=${offset}&limit=${limit}`;
                                    const syncResponse = await fetch(syncUrl);
                                    const syncData = await syncResponse.json();
                                    
                                    if (!syncData.success) {
                                        alert('Error during synchronization: ' + syncData.message);
                                        statusText.innerText = 'Synchronization failed. Check error logs.';
                                        statusText.style.color = '#ef4444';
                                        progressBar.style.background = '#ef4444';
                                        return;
                                    }
                                    
                                    offset += limit;
                                    const pct = Math.min(Math.round((offset / totalItems) * 100), 100);
                                    progressBar.style.width = `${pct}%`;
                                    progressLabel.innerText = `${pct}%`;
                                    progressCount.innerText = `${Math.min(offset, totalItems)} / ${totalItems} items`;
                                }
                                
                                statusText.innerText = 'Database synchronization successfully completed!';
                                statusText.style.color = '#10b981';
                                progressBar.style.background = '#10b981';
                                
                                setTimeout(() => {
                                    window.location.href = '?tab=dashboard&success=Sync+completed+successfully';
                                }, 1500);
                                
                            } catch (err) {
                                console.error(err);
                                alert('An unexpected network error occurred: ' + err.message);
                                progressContainer.style.display = 'none';
                            }
                        }
                        </script>
                    </div>
                </div>
            <?php endif; ?>

            <!-- Sections Tab -->
            <?php if ($tab === 'sections'): ?>
                <div class="card" style="margin-bottom: 2rem;">
                    <h3>Add or Update Section Content</h3>
                    <form action="?tab=sections" method="POST" style="margin-top: 1rem;">
                        <input type="hidden" name="action" value="update_section">
                        
                        <div class="form-row">
                            <div class="form-group">
                                <label class="form-label" for="page">Page Reference</label>
                                <input type="text" name="page" id="page" class="form-control" placeholder="e.g. home, about, services" required>
                            </div>
                            <div class="form-group">
                                <label class="form-label" for="section_key">Section Key (Unique)</label>
                                <input type="text" name="section_key" id="section_key" class="form-control" placeholder="e.g. hero_title, footer_desc" required>
                            </div>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="title">Title/Header Line</label>
                            <input type="text" name="title" id="title" class="form-control" placeholder="Header text">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="content">Body Content / Paragraph</label>
                            <textarea name="content" id="content" class="form-control" placeholder="HTML code or text allowed"></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Section</button>
                    </form>
                </div>

                <div class="card">
                    <h3>Existing Content Sections</h3>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Page</th>
                                <th>Key</th>
                                <th>Title</th>
                                <th>Content Snippet</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $sections = $db->query("SELECT * FROM content_sections ORDER BY page, section_key")->fetchAll();
                            if (empty($sections)):
                            ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; color: var(--text-muted);">No custom content sections created yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($sections as $s): ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($s['page']); ?></strong></td>
                                        <td><code><?php echo htmlspecialchars($s['section_key']); ?></code></td>
                                        <td><?php echo htmlspecialchars($s['title'] ?? ''); ?></td>
                                        <td><?php echo htmlspecialchars(substr(strip_tags($s['content'] ?? ''), 0, 50)) . (strlen($s['content'] ?? '') > 50 ? '...' : ''); ?></td>
                                        <td>
                                            <button class="btn btn-secondary btn-sm" onclick="editSection(<?php echo htmlspecialchars(json_encode($s)); ?>)">Edit</button>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <script>
                    function editSection(data) {
                        document.getElementById('page').value = data.page;
                        document.getElementById('section_key').value = data.section_key;
                        document.getElementById('title').value = data.title || '';
                        document.getElementById('content').value = data.content || '';
                        document.getElementById('page').scrollIntoView({ behavior: 'smooth' });
                    }
                </script>
            <?php endif; ?>

            <!-- Images Tab -->
            <?php if ($tab === 'images'): ?>
                <div class="card" style="margin-bottom: 2rem;">
                    <h3>Manage Page Images & Placeholders</h3>
                    <form action="?tab=images" method="POST" enctype="multipart/form-data" style="margin-top: 1rem;">
                        <input type="hidden" name="action" value="update_image">
                        
                        <div class="form-group">
                            <label class="form-label" for="image_key">Image Key (Identifier used in code)</label>
                            <input type="text" name="image_key" id="image_key" class="form-control" placeholder="e.g. hero_bg, service_icon_1" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="alt_text">Alt Text (For SEO & Screen Readers)</label>
                            <input type="text" name="alt_text" id="alt_text" class="form-control" placeholder="Description of the image">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="image_file">Replace with Real Image File (Optional)</label>
                            <input type="file" name="image_file" id="image_file" class="form-control">
                            <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">Leaves placeholder active if empty. Uploading a file replaces the placeholder representation.</p>
                        </div>

                        <button type="submit" class="btn btn-primary">Save Image Info</button>
                    </form>
                </div>

                <?php
                // Image grouping helper
                function get_image_group($key) {
                    if ($key === 'logo' || $key === 'footer_logo') {
                        return 'Brand Logos & Identity';
                    }
                    if ($key === 'hero_bg' || $key === 'hyster_badge' || $key === 'catalog_cover' || $key === 'catalog_small_icon') {
                        return 'Homepage - General Sections';
                    }
                    if (strpos($key, 'sol_') === 0) {
                        return 'Homepage - Industry Solutions';
                    }
                    if ($key === 'training_bg' || $key === 'careers_img') {
                        return 'Homepage - Training & Careers';
                    }
                    if (strpos($key, 'collab_') === 0 || strpos($key, 'web_') === 0) {
                        return 'Homepage - Collaborators & Affiliated Websites';
                    }
                    if (strpos($key, 'app_') === 0 || strpos($key, 'google_') === 0) {
                        return 'Homepage - Mobile B2B App Badges';
                    }
                    if (strpos($key, 'insta_') === 0) {
                        return 'Homepage - Instagram Feed Mockups';
                    }
                    if ($key === 'parts_collage') {
                        return 'Spare Parts Page';
                    }
                    if (strpos($key, 'about_') === 0) {
                        return 'About Us Page';
                    }
                    if ($key === 'rentals_banner_bg') {
                        return 'Forklift Rentals Page';
                    }
                    if ($key === 'sell_machine_banner') {
                        return 'Sell Your Machine Page';
                    }
                    if (strpos($key, 'repairs_') === 0) {
                        return 'Repairs & Services Page';
                    }
                    return 'Miscellaneous / General';
                }

                $images = $db->query("SELECT * FROM site_images ORDER BY image_key")->fetchAll();
                
                // Group images
                $groups = [];
                foreach ($images as $img) {
                    $grpName = get_image_group($img['image_key']);
                    $groups[$grpName][] = $img;
                }
                
                // Sort groups for consistent display order
                ksort($groups);
                ?>

                <h3 style="margin-top: 2rem; margin-bottom: 1.5rem; font-weight: 700; color: var(--primary-blue);">Grouped Page Image Catalog</h3>

                <?php foreach ($groups as $groupTitle => $groupImages): ?>
                    <div class="card" style="margin-bottom: 2rem; border-top: 4px solid var(--primary-blue);">
                        <h4 style="font-weight: 700; font-size: 1.1rem; color: var(--primary-blue); margin-bottom: 1.25rem;">
                            <?php echo $groupTitle; ?> (<?php echo count($groupImages); ?>)
                        </h4>
                        
                        <div style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 1.5rem;">
                            <?php foreach ($groupImages as $img): ?>
                                <div style="background: #ffffff; border: 1px solid var(--border-gray); border-radius: 6px; padding: 1.25rem; display: flex; flex-direction: column; justify-content: space-between; box-shadow: 0 1px 3px rgba(0,0,0,0.02); transition: transform 0.2s, box-shadow 0.2s;" class="image-card">
                                    <div>
                                        <!-- Preview Thumbnail -->
                                        <div style="height: 140px; background: #f8fafc; border-radius: 4px; display: flex; align-items: center; justify-content: center; overflow: hidden; margin-bottom: 0.85rem; border: 1px dashed var(--border-gray);">
                                            <img src="../<?php echo htmlspecialchars($img['image_path']); ?>" alt="preview" style="max-height: 100%; max-width: 100%; object-fit: contain;">
                                        </div>
                                        
                                        <!-- Details -->
                                        <div style="margin-bottom: 0.85rem;">
                                            <strong style="display: block; font-size: 0.95rem; color: var(--text-dark); margin-bottom: 0.15rem;">
                                                <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $img['image_key']))); ?>
                                            </strong>
                                            <code style="font-size: 0.75rem; color: var(--text-muted); display: block; margin-bottom: 0.5rem;"><?php echo htmlspecialchars($img['image_key']); ?></code>
                                            <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.5rem; line-height: 1.35; height: 36px; overflow: hidden; text-overflow: ellipsis; display: -webkit-box; -webkit-line-clamp: 2; -webkit-box-orient: vertical;">
                                                <strong>Alt text:</strong> <?php echo htmlspecialchars($img['alt_text'] ?: 'No alternate text'); ?>
                                            </p>
                                        </div>
                                    </div>
                                    
                                    <!-- Actions -->
                                    <div style="border-top: 1px solid var(--border-gray); padding-top: 0.75rem; display: flex; justify-content: space-between; align-items: center; margin-top: auto;">
                                        <span style="font-size: 0.75rem; color: var(--text-muted); font-weight: 500; text-overflow: ellipsis; overflow: hidden; white-space: nowrap; max-width: 140px;" title="<?php echo htmlspecialchars($img['image_path']); ?>">
                                            <?php echo htmlspecialchars(basename($img['image_path'])); ?>
                                        </span>
                                        <button class="btn btn-secondary btn-sm" style="padding: 0.35rem 0.85rem; font-weight: 600;" onclick="editImage(<?php echo htmlspecialchars(json_encode($img)); ?>)">
                                            Replace
                                        </button>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    </div>
                <?php endforeach; ?>

                <script>
                    function editImage(data) {
                        document.getElementById('image_key').value = data.image_key;
                        document.getElementById('alt_text').value = data.alt_text || '';
                        document.getElementById('image_key').scrollIntoView({ behavior: 'smooth' });
                    }
                </script>
            <?php endif; ?>

            <!-- Settings Tab -->
            <?php if ($tab === 'settings'): ?>
                <div class="card">
                    <h3>Edit Global Site Settings</h3>
                    <form action="?tab=settings" method="POST" style="margin-top: 1rem;">
                        <input type="hidden" name="action" value="update_settings">
                        
                        <?php
                        $settings = $db->query("SELECT * FROM site_settings ORDER BY key")->fetchAll();
                        foreach ($settings as $setting):
                        ?>
                            <div class="form-group">
                                <label class="form-label" for="setting_<?php echo htmlspecialchars($setting['key']); ?>">
                                    <?php echo htmlspecialchars(ucwords(str_replace('_', ' ', $setting['key']))); ?>
                                </label>
                                <input type="text" name="settings[<?php echo htmlspecialchars($setting['key']); ?>]" 
                                       id="setting_<?php echo htmlspecialchars($setting['key']); ?>" 
                                       class="form-control" 
                                       value="<?php echo htmlspecialchars($setting['value']); ?>" required>
                            </div>
                        <?php endforeach; ?>

                        <button type="submit" class="btn btn-primary" style="margin-top: 1rem;">Save Global Settings</button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Security Tab -->
            <?php if ($tab === 'security'): ?>
                <div class="card">
                    <h3>Change Admin Password</h3>
                    <form action="?tab=security" method="POST" style="margin-top: 1rem;">
                        <input type="hidden" name="action" value="change_password">
                        
                        <div class="form-group">
                            <label class="form-label" for="old_password">Current Password</label>
                            <input type="password" name="old_password" id="old_password" class="form-control" required autocomplete="current-password">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="new_password">New Password</label>
                            <input type="password" name="new_password" id="new_password" class="form-control" required autocomplete="new-password">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="confirm_password">Confirm New Password</label>
                            <input type="password" name="confirm_password" id="confirm_password" class="form-control" required autocomplete="new-password">
                        </div>

                        <button type="submit" class="btn btn-primary" style="margin-top: 1rem;">Update Password</button>
                    </form>
                </div>
            <?php endif; ?>

            <!-- Sync History Tab -->
            <?php if ($tab === 'sync_history'): ?>
                <div class="card">
                    <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 1.5rem;">
                        <h3>1C Sync History Logs</h3>
                        <form action="?tab=sync_history" method="POST" style="margin: 0;">
                            <input type="hidden" name="action" value="sync_catalog">
                            <input type="hidden" name="sync_source" value="soap">
                            <button type="submit" class="hz-filter-btn" style="padding: 0.5rem 1.25rem; background: #eab308; color: #000000; font-weight: 700; border: none; border-radius: 4px; cursor: pointer;">
                                ⚡ Trigger SOAP Sync Now
                            </button>
                        </form>
                    </div>

                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Sync Time</th>
                                <th>Source</th>
                                <th>Status</th>
                                <th>Result Message</th>
                                <th>Changes Details</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $logs = $db->query("SELECT * FROM sync_logs ORDER BY id DESC LIMIT 30")->fetchAll();
                            if (empty($logs)):
                            ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; color: var(--text-muted); padding: 2rem;">No sync execution logs recorded yet. Run a synchronization to generate logs.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($logs as $log): 
                                    $added = json_decode($log['added_items'] ?? '[]', true) ?: [];
                                    $removed = json_decode($log['removed_items'] ?? '[]', true) ?: [];
                                    $updated = json_decode($log['updated_items'] ?? '[]', true) ?: [];
                                    $hasChanges = !empty($added) || !empty($removed) || !empty($updated);
                                ?>
                                    <tr>
                                        <td><strong><?php echo htmlspecialchars($log['sync_time']); ?></strong></td>
                                        <td><span class="badge" style="background: #efefef; color: #333; padding: 0.25rem 0.5rem; border-radius: 4px; font-weight: 600; font-size: 0.75rem;"><?php echo htmlspecialchars($log['source']); ?></span></td>
                                        <td>
                                            <?php if ($log['status'] === 'Success'): ?>
                                                <span style="background: #dcfce7; color: #166534; padding: 0.25rem 0.65rem; border-radius: 4px; font-weight: 700; font-size: 0.8rem;">Success</span>
                                            <?php else: ?>
                                                <span style="background: #fee2e2; color: #991b1b; padding: 0.25rem 0.65rem; border-radius: 4px; font-weight: 700; font-size: 0.8rem;">Failed</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><span style="font-size: 0.9rem;"><?php echo htmlspecialchars($log['message']); ?></span></td>
                                        <td>
                                            <?php if ($hasChanges): ?>
                                                <button class="btn btn-secondary btn-sm" onclick="toggleDetails(<?php echo $log['id']; ?>)">
                                                    Show Changes (<?php echo count($added) + count($removed) + count($updated); ?>)
                                                </button>
                                            <?php else: ?>
                                                <span style="color: var(--text-muted); font-size: 0.85rem;">No changes</span>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php if ($hasChanges): ?>
                                        <tr id="details-<?php echo $log['id']; ?>" style="display: none; background: #fafafa;">
                                            <td colspan="5" style="padding: 1.5rem; border-top: none;">
                                                <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
                                                    <!-- Added Items -->
                                                    <?php if (!empty($added)): ?>
                                                        <div style="border: 1px solid #dcfce7; border-radius: 6px; padding: 1rem; background: #ffffff;">
                                                            <h5 style="color: #166534; font-weight: 700; margin-bottom: 0.5rem; border-bottom: 1px solid #dcfce7; padding-bottom: 0.25rem;">Added to Web (<?php echo count($added); ?>)</h5>
                                                            <ul style="list-style: none; padding: 0; margin: 0; max-height: 200px; overflow-y: auto;">
                                                                <?php foreach ($added as $item): ?>
                                                                    <li style="font-size: 0.85rem; padding: 0.25rem 0; border-bottom: 1px dashed #efefef;">
                                                                        <code><?php echo htmlspecialchars($item['code']); ?></code> - <?php echo htmlspecialchars($item['name']); ?>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        </div>
                                                    <?php endif; ?>

                                                    <!-- Removed Items -->
                                                    <?php if (!empty($removed)): ?>
                                                        <div style="border: 1px solid #fee2e2; border-radius: 6px; padding: 1rem; background: #ffffff;">
                                                            <h5 style="color: #991b1b; font-weight: 700; margin-bottom: 0.5rem; border-bottom: 1px solid #fee2e2; padding-bottom: 0.25rem;">Removed from Web (<?php echo count($removed); ?>)</h5>
                                                            <ul style="list-style: none; padding: 0; margin: 0; max-height: 200px; overflow-y: auto;">
                                                                <?php foreach ($removed as $item): ?>
                                                                    <li style="font-size: 0.85rem; padding: 0.25rem 0; border-bottom: 1px dashed #efefef;">
                                                                        <code><?php echo htmlspecialchars($item['code']); ?></code> - <?php echo htmlspecialchars($item['name']); ?>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        </div>
                                                    <?php endif; ?>

                                                    <!-- Updated Items -->
                                                    <?php if (!empty($updated)): ?>
                                                        <div style="border: 1px solid #fef9c3; border-radius: 6px; padding: 1rem; background: #ffffff;">
                                                            <h5 style="color: #854d0e; font-weight: 700; margin-bottom: 0.5rem; border-bottom: 1px solid #fef9c3; padding-bottom: 0.25rem;">Updated Details (<?php echo count($updated); ?>)</h5>
                                                            <ul style="list-style: none; padding: 0; margin: 0; max-height: 200px; overflow-y: auto;">
                                                                <?php foreach ($updated as $item): ?>
                                                                    <li style="font-size: 0.85rem; padding: 0.25rem 0; border-bottom: 1px dashed #efefef;">
                                                                        <code><?php echo htmlspecialchars($item['code']); ?></code> - <?php echo htmlspecialchars($item['name']); ?>
                                                                    </li>
                                                                <?php endforeach; ?>
                                                            </ul>
                                                        </div>
                                                    <?php endif; ?>
                                                </div>
                                            </td>
                                        </tr>
                                    <?php endif; ?>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <script>
                    function toggleDetails(id) {
                        var el = document.getElementById('details-' + id);
                        if (el.style.display === 'none') {
                            el.style.display = 'table-row';
                        } else {
                            el.style.display = 'none';
                        }
                    }
                </script>
            <?php endif; ?>

            <!-- FAQs Tab -->
            <?php if ($tab === 'faqs'): ?>
                <div class="card" style="margin-bottom: 2rem;">
                    <h3 id="form-title">Add New FAQ Accordion</h3>
                    <form action="?tab=faqs" method="POST" style="margin-top: 1rem;">
                        <input type="hidden" name="action" id="faq-action" value="add_faq">
                        <input type="hidden" name="id" id="faq-id" value="">
                        
                        <div class="form-group">
                            <label class="form-label" for="faq-question">Question</label>
                            <input type="text" name="question" id="faq-question" class="form-control" placeholder="Enter the question text" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="faq-answer">Answer</label>
                            <textarea name="answer" id="faq-answer" class="form-control" placeholder="Enter the answer content" required></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="faq-order">Sort Order (Numbers lower display first)</label>
                            <input type="number" name="sort_order" id="faq-order" class="form-control" value="0">
                        </div>

                        <div style="display: flex; gap: 1rem;">
                            <button type="submit" class="btn btn-primary" id="faq-submit-btn">Add FAQ</button>
                            <button type="button" class="btn btn-secondary" id="faq-cancel-btn" style="display: none;" onclick="resetFaqForm()">Cancel Edit</button>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <h3>Existing FAQs</h3>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Sort</th>
                                <th>Question</th>
                                <th>Answer Snippet</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $faqs = $db->query("SELECT * FROM faqs ORDER BY sort_order, id")->fetchAll();
                            if (empty($faqs)):
                            ?>
                                <tr>
                                    <td colspan="4" style="text-align: center; color: var(--text-muted);">No FAQs created yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($faqs as $f): ?>
                                    <tr>
                                        <td><code><?php echo htmlspecialchars($f['sort_order']); ?></code></td>
                                        <td><strong><?php echo htmlspecialchars($f['question']); ?></strong></td>
                                        <td><?php echo htmlspecialchars(substr($f['answer'], 0, 80)) . (strlen($f['answer']) > 80 ? '...' : ''); ?></td>
                                        <td style="display: flex; gap: 0.5rem;">
                                            <button class="btn btn-secondary btn-sm" onclick="editFaq(<?php echo htmlspecialchars(json_encode($f)); ?>)">Edit</button>
                                            <form action="?tab=faqs" method="POST" onsubmit="return confirm('Are you sure you want to delete this FAQ?');" style="display: inline;">
                                                <input type="hidden" name="action" value="delete_faq">
                                                <input type="hidden" name="id" value="<?php echo $f['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <script>
                    function editFaq(data) {
                        document.getElementById('form-title').innerText = 'Edit FAQ Accordion';
                        document.getElementById('faq-action').value = 'update_faq';
                        document.getElementById('faq-id').value = data.id;
                        document.getElementById('faq-question').value = data.question;
                        document.getElementById('faq-answer').value = data.answer;
                        document.getElementById('faq-order').value = data.sort_order;
                        document.getElementById('faq-submit-btn').innerText = 'Save Changes';
                        document.getElementById('faq-cancel-btn').style.display = 'inline-flex';
                        document.getElementById('form-title').scrollIntoView({ behavior: 'smooth' });
                    }

                    function resetFaqForm() {
                        document.getElementById('form-title').innerText = 'Add New FAQ Accordion';
                        document.getElementById('faq-action').value = 'add_faq';
                        document.getElementById('faq-id').value = '';
                        document.getElementById('faq-question').value = '';
                        document.getElementById('faq-answer').value = '';
                        document.getElementById('faq-order').value = '0';
                        document.getElementById('faq-submit-btn').innerText = 'Add FAQ';
                        document.getElementById('faq-cancel-btn').style.display = 'none';
                    }
                </script>
            <?php endif; ?>

            <!-- Blog Posts Tab -->
            <?php if ($tab === 'blog'): ?>
                <div class="card" style="margin-bottom: 2rem;">
                    <h3 id="blog-form-title">Add New Blog Post</h3>
                    <form action="?tab=blog" method="POST" enctype="multipart/form-data" style="margin-top: 1rem;">
                        <input type="hidden" name="action" id="blog-action" value="add_blog">
                        <input type="hidden" name="id" id="blog-id" value="">
                        
                        <div class="form-group">
                            <label class="form-label" for="blog-title">Title</label>
                            <input type="text" name="title" id="blog-title" class="form-control" placeholder="Enter title" required oninput="generateSlug(this.value)">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="blog-slug">URL Slug (e.g. dynamic-parts-watering)</label>
                            <input type="text" name="slug" id="blog-slug" class="form-control" placeholder="URL slug" required>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="blog-badge">Badge Text (Optional, e.g. Safety Guidelines)</label>
                            <input type="text" name="badge_text" id="blog-badge" class="form-control" placeholder="e.g. Safety Guidelines">
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="blog-excerpt">Excerpt (Short snippet shown in grid)</label>
                            <textarea name="excerpt" id="blog-excerpt" class="form-control" placeholder="Short description" required></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="blog-content">Full Content / Body</label>
                            <textarea name="content" id="blog-content" class="form-control" style="min-height: 200px;" placeholder="Full article content (HTML allowed)" required></textarea>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="blog-image">Article Image File</label>
                            <input type="file" name="blog_image" id="blog-image" class="form-control">
                            <p style="font-size: 0.85rem; color: var(--text-muted); margin-top: 0.25rem;">Leaves existing/placeholder active if empty.</p>
                        </div>

                        <div class="form-group">
                            <label class="form-label" for="blog-date">Publication Date</label>
                            <input type="date" name="created_at" id="blog-date" class="form-control" value="<?php echo date('Y-m-d'); ?>">
                        </div>

                        <div style="display: flex; gap: 1rem;">
                            <button type="submit" class="btn btn-primary" id="blog-submit-btn">Publish Post</button>
                            <button type="button" class="btn btn-secondary" id="blog-cancel-btn" style="display: none;" onclick="resetBlogForm()">Cancel Edit</button>
                        </div>
                    </form>
                </div>

                <div class="card">
                    <h3>Published Articles</h3>
                    <table class="admin-table">
                        <thead>
                            <tr>
                                <th>Date</th>
                                <th>Title</th>
                                <th>Badge</th>
                                <th>Image Preview</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $blog_posts = $db->query("SELECT * FROM blog_posts ORDER BY created_at DESC")->fetchAll();
                            if (empty($blog_posts)):
                            ?>
                                <tr>
                                    <td colspan="5" style="text-align: center; color: var(--text-muted);">No blog posts published yet.</td>
                                </tr>
                            <?php else: ?>
                                <?php foreach ($blog_posts as $bp): ?>
                                    <tr>
                                        <td><code><?php echo date('Y-m-d', strtotime($bp['created_at'])); ?></code></td>
                                        <td><strong><?php echo htmlspecialchars($bp['title']); ?></strong></td>
                                        <td><span style="font-size: 0.8rem; background-color: var(--primary-light); color: var(--primary); padding: 0.2rem 0.5rem; border-radius: 3px;"><?php echo htmlspecialchars($bp['badge_text'] ?: 'None'); ?></span></td>
                                        <td>
                                            <img src="../<?php echo htmlspecialchars($bp['image_path']); ?>" alt="preview" style="max-height: 40px; border-radius: 4px;">
                                        </td>
                                        <td style="display: flex; gap: 0.5rem;">
                                            <button class="btn btn-secondary btn-sm" onclick="editBlog(<?php echo htmlspecialchars(json_encode($bp)); ?>)">Edit</button>
                                            <form action="?tab=blog" method="POST" onsubmit="return confirm('Are you sure you want to delete this blog post?');" style="display: inline;">
                                                <input type="hidden" name="action" value="delete_blog">
                                                <input type="hidden" name="id" value="<?php echo $bp['id']; ?>">
                                                <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <script>
                    function generateSlug(text) {
                        const slug = text.toLowerCase()
                            .replace(/[^a-z0-9\s-]/g, '')
                            .replace(/\s+/g, '-')
                            .replace(/-+/g, '-');
                        
                        if (document.getElementById('blog-action').value === 'add_blog') {
                            document.getElementById('blog-slug').value = slug;
                        }
                    }

                    function editBlog(data) {
                        document.getElementById('blog-form-title').innerText = 'Edit Blog Post';
                        document.getElementById('blog-action').value = 'update_blog';
                        document.getElementById('blog-id').value = data.id;
                        document.getElementById('blog-title').value = data.title;
                        document.getElementById('blog-slug').value = data.slug;
                        document.getElementById('blog-badge').value = data.badge_text || '';
                        document.getElementById('blog-excerpt').value = data.excerpt || '';
                        document.getElementById('blog-content').value = data.content || '';
                        
                        const pubDate = new Date(data.created_at);
                        const formattedDate = pubDate.toISOString().split('T')[0];
                        document.getElementById('blog-date').value = formattedDate;

                        document.getElementById('blog-submit-btn').innerText = 'Save Changes';
                        document.getElementById('blog-cancel-btn').style.display = 'inline-flex';
                        document.getElementById('blog-form-title').scrollIntoView({ behavior: 'smooth' });
                    }

                    function resetBlogForm() {
                        document.getElementById('blog-form-title').innerText = 'Add New Blog Post';
                        document.getElementById('blog-action').value = 'add_blog';
                        document.getElementById('blog-id').value = '';
                        document.getElementById('blog-title').value = '';
                        document.getElementById('blog-slug').value = '';
                        document.getElementById('blog-badge').value = '';
                        document.getElementById('blog-excerpt').value = '';
                        document.getElementById('blog-content').value = '';
                        document.getElementById('blog-date').value = '<?php echo date('Y-m-d'); ?>';
                        document.getElementById('blog-submit-btn').innerText = 'Publish Post';
                        document.getElementById('blog-cancel-btn').style.display = 'none';
                    }
                </script>
            <?php endif; ?>

            <!-- Rental Requests Tab -->
            <?php if ($tab === 'rentals'): ?>
                <div class="card">
                    <h3>Inquiries & Requests</h3>
                    <p style="margin-bottom: 1.5rem; color: var(--text-muted);">View all forklift rental request submissions received from the rentals page form.</p>
                    
                    <div style="overflow-x: auto;">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Submitted</th>
                                    <th>Client Info</th>
                                    <th>Requirements</th>
                                    <th>Dates</th>
                                    <th>Address / Details</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $requests = $db->query("SELECT * FROM rental_requests ORDER BY created_at DESC")->fetchAll();
                                if (empty($requests)):
                                ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem 0;">No rental inquiries received yet.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($requests as $r): ?>
                                        <tr>
                                            <td><small><?php echo date('Y-m-d H:i', strtotime($r['created_at'])); ?></small></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($r['full_name']); ?></strong><br>
                                                <small>📞 <?php echo htmlspecialchars($r['phone']); ?></small><br>
                                                <small>👤 Entity: <?php echo htmlspecialchars($r['entity'] ?: 'N/A'); ?></small>
                                            </td>
                                            <td>
                                                <small>⚖️ Weight: <?php echo htmlspecialchars($r['max_weight']); ?></small><br>
                                                <small>📏 Height: <?php echo htmlspecialchars($r['max_height']); ?></small><br>
                                                <small>🍴 Forks: <?php echo htmlspecialchars($r['forks_length']); ?></small><br>
                                                <small>⚙️ Cond: <?php echo htmlspecialchars($r['condition']); ?></small>
                                            </td>
                                            <td>
                                                <small>From: <?php echo htmlspecialchars($r['from_date']); ?></small><br>
                                                <small>Till: <?php echo htmlspecialchars($r['till_date']); ?></small>
                                            </td>
                                            <td>
                                                <small>📍 <?php echo htmlspecialchars($r['location']); ?></small><br>
                                                <small>🏠 <?php echo htmlspecialchars($r['full_address']); ?></small>
                                                <?php if (!empty($r['application_work']) || !empty($r['specifications'])): ?>
                                                    <br><button class="btn btn-secondary btn-sm" style="padding: 0.15rem 0.4rem; font-size: 0.75rem; margin-top: 0.25rem;" onclick="showDetails(<?php echo htmlspecialchars(json_encode($r)); ?>)">View Notes</button>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <form action="?tab=rentals" method="POST" onsubmit="return confirm('Are you sure you want to delete this rental request?');">
                                                    <input type="hidden" name="action" value="delete_rental">
                                                    <input type="hidden" name="id" value="<?php echo $r['id']; ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Custom Modal for showing Notes/Details -->
                <div class="rental-modal-backdrop" id="notesModal">
                    <div class="rental-modal-card" style="max-width: 500px;">
                        <div class="rental-modal-header" style="background-color: #f8fafc;">
                            <h3>Additional Inquiries Notes</h3>
                            <button class="rental-modal-close-btn" onclick="closeNotes()">&times;</button>
                        </div>
                        <div class="rental-modal-body">
                            <div style="margin-bottom: 1.5rem;">
                                <strong>Application of Work:</strong>
                                <p id="note-app" style="margin-top: 0.5rem; color: #475569; font-size: 0.95rem; line-height: 1.6;"></p>
                            </div>
                            <div>
                                <strong>Additional Specifications:</strong>
                                <p id="note-specs" style="margin-top: 0.5rem; color: #475569; font-size: 0.95rem; line-height: 1.6;"></p>
                            </div>
                        </div>
                    </div>
                </div>

                <script>
                    function showDetails(data) {
                        document.getElementById('note-app').innerText = data.application_work || 'None provided';
                        document.getElementById('note-specs').innerText = data.specifications || 'None provided';
                        document.getElementById('notesModal').classList.add('open');
                    }
                    function closeNotes() {
                        document.getElementById('notesModal').classList.remove('open');
                    }
                    window.addEventListener('click', (e) => {
                        const modal = document.getElementById('notesModal');
                        if (e.target === modal) {
                            modal.classList.remove('open');
                        }
                    });
                </script>
            <?php endif; ?>

            <!-- Sell Machine Requests Tab -->
            <?php if ($tab === 'sell_machines'): ?>
                <div class="card">
                    <h3>Equipment Sale Inquiries</h3>
                    <p style="margin-bottom: 1.5rem; color: var(--text-muted);">View machine valuation submissions received from the Sell Your Machine form.</p>
                    
                    <div style="overflow-x: auto;">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Submitted</th>
                                    <th>Client Info</th>
                                    <th>Machine Info</th>
                                    <th>Photo</th>
                                    <th>Details / Notes</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $sells = $db->query("SELECT * FROM sell_machine_requests ORDER BY created_at DESC")->fetchAll();
                                if (empty($sells)):
                                ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem 0;">No sale inquiries received yet.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($sells as $s): ?>
                                        <tr>
                                            <td><small><?php echo date('Y-m-d H:i', strtotime($s['created_at'])); ?></small></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($s['full_name']); ?></strong><br>
                                                <small>📞 <?php echo htmlspecialchars($s['phone']); ?></small><br>
                                                <small>✉️ <?php echo htmlspecialchars($s['email']); ?></small><br>
                                                <small>📍 Location: <?php echo htmlspecialchars($s['location']); ?></small>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($s['model']); ?></strong><br>
                                                <small>⚡ Power: <?php echo htmlspecialchars($s['power']); ?></small><br>
                                                <small>#️⃣ Serial: <?php echo htmlspecialchars($s['serial_number'] ?: 'N/A'); ?></small>
                                            </td>
                                            <td>
                                                <?php if (!empty($s['photo_path']) && file_exists(__DIR__ . '/../' . $s['photo_path'])): ?>
                                                    <a href="../<?php echo htmlspecialchars($s['photo_path']); ?>" target="_blank">
                                                        <img src="../<?php echo htmlspecialchars($s['photo_path']); ?>" alt="Machine Photo" style="width: 80px; height: 60px; object-fit: cover; border-radius: 4px; border: 1px solid var(--border);">
                                                    </a>
                                                <?php else: ?>
                                                    <small style="color: var(--text-muted);">No photo</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <?php if (!empty($s['condition_details'])): ?>
                                                    <button class="btn btn-secondary btn-sm" style="padding: 0.15rem 0.4rem; font-size: 0.75rem;" onclick="showSellDetails(<?php echo htmlspecialchars(json_encode($s['condition_details'])); ?>)">View Details</button>
                                                <?php else: ?>
                                                    <small style="color: var(--text-muted);">No notes</small>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <form action="?tab=sell_machines" method="POST" onsubmit="return confirm('Are you sure you want to delete this sale inquiry?');">
                                                    <input type="hidden" name="action" value="delete_sell">
                                                    <input type="hidden" name="id" value="<?php echo $s['id']; ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Custom Modal for showing Notes/Details -->
                <div class="rental-modal-backdrop" id="sellNotesModal">
                    <div class="rental-modal-card" style="max-width: 500px;">
                        <div class="rental-modal-header" style="background-color: #f8fafc;">
                            <h3>Condition / Machine Details</h3>
                            <button class="rental-modal-close-btn" onclick="closeSellNotes()">&times;</button>
                        </div>
                        <div class="rental-modal-body">
                            <p id="sell-note-content" style="color: #475569; font-size: 0.95rem; line-height: 1.6; white-space: pre-line;"></p>
                        </div>
                    </div>
                </div>

                <script>
                    function showSellDetails(notes) {
                        document.getElementById('sell-note-content').innerText = notes || 'None provided';
                        document.getElementById('sellNotesModal').classList.add('open');
                    }
                    function closeSellNotes() {
                        document.getElementById('sellNotesModal').classList.remove('open');
                    }
                    window.addEventListener('click', (e) => {
                        const modal = document.getElementById('sellNotesModal');
                        if (e.target === modal) {
                            modal.classList.remove('open');
                        }
                    });
                </script>
            <?php endif; ?>

            <!-- Part Requests Tab -->
            <?php if ($tab === 'part_requests'): ?>
                <div class="card">
                    <h3>Forklift Spare Part Requests</h3>
                    <p style="margin-bottom: 1.5rem; color: var(--text-muted);">View all spare parts request submissions received from the spare parts page form.</p>
                    
                    <div style="overflow-x: auto;">
                        <table class="admin-table">
                            <thead>
                                <tr>
                                    <th>Submitted</th>
                                    <th>Client Info</th>
                                    <th>Part Details</th>
                                    <th>Truck Info</th>
                                    <th>Location</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $parts = $db->query("SELECT * FROM part_requests ORDER BY created_at DESC")->fetchAll();
                                if (empty($parts)):
                                ?>
                                    <tr>
                                        <td colspan="6" style="text-align: center; color: var(--text-muted); padding: 2rem 0;">No spare part requests received yet.</td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($parts as $p): ?>
                                        <tr>
                                            <td><small><?php echo date('Y-m-d H:i', strtotime($p['created_at'])); ?></small></td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($p['full_name']); ?></strong><br>
                                                <small>📞 <?php echo htmlspecialchars($p['phone']); ?></small><br>
                                                <small>👤 Entity: <?php echo htmlspecialchars($p['entity']); ?></small>
                                            </td>
                                            <td>
                                                <strong>Qty: <?php echo htmlspecialchars($p['quantity']); ?></strong><br>
                                                <button class="btn btn-secondary btn-sm" style="padding: 0.15rem 0.4rem; font-size: 0.75rem; margin-top: 0.25rem;" onclick="showPartDesc(<?php echo htmlspecialchars(json_encode($p['part_description'])); ?>)">View Description</button>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($p['brand']); ?></strong> - <?php echo htmlspecialchars($p['model']); ?><br>
                                                <small>#️⃣ Serial: <?php echo htmlspecialchars($p['serial_number'] ?: 'N/A'); ?></small>
                                            </td>
                                            <td>
                                                <small>📍 <?php echo htmlspecialchars($p['location']); ?></small>
                                            </td>
                                            <td>
                                                <form action="?tab=part_requests" method="POST" onsubmit="return confirm('Are you sure you want to delete this part request?');">
                                                    <input type="hidden" name="action" value="delete_part_request">
                                                    <input type="hidden" name="id" value="<?php echo $p['id']; ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>

                <!-- Custom Modal for showing Part Description -->
                <div class="rental-modal-backdrop" id="partDescModal">
                    <div class="rental-modal-card" style="max-width: 500px;">
                        <div class="rental-modal-header" style="background-color: #f8fafc;">
                            <h3>Part Sourcing Description</h3>
                            <button class="rental-modal-close-btn" onclick="closePartDesc()">&times;</button>
                        </div>
                        <div class="rental-modal-body">
                            <p id="part-desc-content" style="color: #475569; font-size: 0.95rem; line-height: 1.6; white-space: pre-line;"></p>
                        </div>
                    </div>
                </div>

                <script>
                    function showPartDesc(notes) {
                        document.getElementById('part-desc-content').innerText = notes || 'None provided';
                        document.getElementById('partDescModal').classList.add('open');
                    }
                    function closePartDesc() {
                        document.getElementById('partDescModal').classList.remove('open');
                    }
                    window.addEventListener('click', (e) => {
                        const modal = document.getElementById('partDescModal');
                        if (e.target === modal) {
                            modal.classList.remove('open');
                        }
                    });
                </script>
            <?php endif; ?>

            <!-- Machine Inquiries Tab -->
            <?php if ($tab === 'machine_requests'): ?>
                <div class="card">
                    <h3>Machine Purchase & Rental Inquiries</h3>
                    <p style="color: var(--text-muted); margin-bottom: 1.5rem;">Submitted interest forms from the dynamic product profile pages.</p>
                    
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Client / Entity</th>
                                    <th>Machine</th>
                                    <th>Type</th>
                                    <th>Location</th>
                                    <th>Period / Dates</th>
                                    <th>Submitted At</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $inqs = $db->query("SELECT * FROM machine_requests ORDER BY created_at DESC")->fetchAll();
                                if (empty($inqs)): 
                                ?>
                                    <tr>
                                        <td colspan="7" style="text-align: center; color: var(--text-muted); padding: 2rem;">
                                            No inquiries received yet.
                                        </td>
                                    </tr>
                                <?php else: ?>
                                    <?php foreach ($inqs as $i): ?>
                                        <tr>
                                            <td>
                                                <strong><?php echo htmlspecialchars($i['full_name']); ?></strong><br>
                                                <small>📞 <?php echo htmlspecialchars($i['phone']); ?></small><br>
                                                <small>🏢 Entity: <?php echo htmlspecialchars($i['entity']); ?></small>
                                            </td>
                                            <td>
                                                <strong><?php echo htmlspecialchars($i['machine_name']); ?></strong><br>
                                                <small>ID: #<?php echo $i['machine_id']; ?></small>
                                            </td>
                                            <td>
                                                <span class="badge" style="background-color: <?php echo $i['request_type'] === 'purchase' ? '#1d4ed8' : '#eab308'; ?>; color:#ffffff; font-size:0.75rem; padding: 0.2rem 0.5rem; border-radius: 4px; display: inline-block;">
                                                    <?php echo strtoupper($i['request_type']); ?>
                                                </span>
                                            </td>
                                            <td>
                                                📍 <?php echo htmlspecialchars($i['location']); ?>
                                            </td>
                                            <td>
                                                <?php if ($i['request_type'] === 'rent'): ?>
                                                    <span style="font-size:0.85rem;">
                                                        <strong>From:</strong> <?php echo htmlspecialchars($i['from_date']); ?><br>
                                                        <strong>Till:</strong> <?php echo htmlspecialchars($i['till_date']); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span style="color:var(--text-muted);">N/A (Purchase)</span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <small><?php echo htmlspecialchars($i['created_at']); ?></small>
                                            </td>
                                            <td>
                                                <form action="?tab=machine_requests" method="POST" onsubmit="return confirm('Are you sure you want to delete this machine inquiry?');">
                                                    <input type="hidden" name="action" value="delete_machine_request">
                                                    <input type="hidden" name="id" value="<?php echo $i['id']; ?>">
                                                    <button type="submit" class="btn btn-danger btn-sm">Delete</button>
                                                </form>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            <?php endif; ?>
        </main>
    </div>
</body>
</html>
