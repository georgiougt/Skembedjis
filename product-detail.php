<?php
// product-detail.php
// Recreated Premium Product Profile page matching screenshots exactly

require_once __DIR__ . '/db.php';

// Handle POST request for purchase and rent inquiries
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    header('Content-Type: application/json');
    
    $action = $_POST['action'];
    $machine_id = (int)($_POST['machine_id'] ?? 0);
    $machine_name = trim($_POST['machine_name'] ?? '');
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $entity = trim($_POST['entity'] ?? '');
    $location = trim($_POST['location'] ?? '');
    
    if (empty($full_name) || empty($phone) || empty($entity) || empty($location)) {
        echo json_encode(['success' => false, 'error' => 'Please fill all required fields.']);
        exit;
    }

    if ($action === 'submit_purchase_inquiry') {
        try {
            $stmt = $db->prepare("
                INSERT INTO machine_requests (machine_id, machine_name, request_type, full_name, phone, entity, location)
                VALUES (:machine_id, :machine_name, 'purchase', :full_name, :phone, :entity, :location)
            ");
            $stmt->execute([
                ':machine_id' => $machine_id,
                ':machine_name' => $machine_name,
                ':full_name' => $full_name,
                ':phone' => $phone,
                ':entity' => $entity,
                ':location' => $location
            ]);
            echo json_encode(['success' => true]);
            exit;
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }

    if ($action === 'submit_rent_inquiry') {
        $from_date = trim($_POST['from_date'] ?? '');
        $till_date = trim($_POST['till_date'] ?? '');
        if (empty($from_date) || empty($till_date)) {
            echo json_encode(['success' => false, 'error' => 'Please specify rental period dates.']);
            exit;
        }
        try {
            $stmt = $db->prepare("
                INSERT INTO machine_requests (machine_id, machine_name, request_type, full_name, phone, entity, location, from_date, till_date)
                VALUES (:machine_id, :machine_name, 'rent', :full_name, :phone, :entity, :location, :from_date, :till_date)
            ");
            $stmt->execute([
                ':machine_id' => $machine_id,
                ':machine_name' => $machine_name,
                ':full_name' => $full_name,
                ':phone' => $phone,
                ':entity' => $entity,
                ':location' => $location,
                ':from_date' => $from_date,
                ':till_date' => $till_date
            ]);
            echo json_encode(['success' => true]);
            exit;
        } catch (PDOException $e) {
            echo json_encode(['success' => false, 'error' => $e->getMessage()]);
            exit;
        }
    }
    
    echo json_encode(['success' => false, 'error' => 'Invalid action.']);
    exit;
}

$slug = trim($_GET['slug'] ?? '');
if (empty($slug)) {
    header('Location: products.php');
    exit;
}

// Fetch primary product
$stmt = $db->prepare("
    SELECT p.*, c.name as category_name, c.slug as category_slug 
    FROM products p 
    JOIN product_categories c ON p.category_id = c.id
    WHERE p.slug = :slug
");
$stmt->execute([':slug' => $slug]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if (!$product) {
    header('Location: products.php');
    exit;
}

// Query related products (same category first, fall back to general products if count < 4)
$stmtRel = $db->prepare("
    SELECT p.*, c.name as category_name, c.slug as category_slug 
    FROM products p 
    JOIN product_categories c ON p.category_id = c.id
    WHERE p.category_id = :cat_id AND p.id != :id
    LIMIT 4
");
$stmtRel->execute([':cat_id' => $product['category_id'], ':id' => $product['id']]);
$related = $stmtRel->fetchAll(PDO::FETCH_ASSOC);

if (count($related) < 4) {
    $needed = 4 - count($related);
    $exclude = array_merge([$product['id']], array_column($related, 'id'));
    $inClause = implode(',', array_fill(0, count($exclude), '?'));
    
    $stmtExtra = $db->prepare("
        SELECT p.*, c.name as category_name, c.slug as category_slug 
        FROM products p 
        JOIN product_categories c ON p.category_id = c.id
        WHERE p.id NOT IN ($inClause)
        LIMIT $needed
    ");
    $stmtExtra->execute($exclude);
    $extra = $stmtExtra->fetchAll(PDO::FETCH_ASSOC);
    $related = array_merge($related, $extra);
}

// Parse gallery images
$gallery = [];
if (!empty($product['gallery_images'])) {
    $gallery = explode(',', $product['gallery_images']);
}
// Add main image as first item in gallery if not already present
if (!in_array($product['photo_path'], $gallery)) {
    array_unshift($gallery, $product['photo_path']);
}

$current_page = 'products.php';
require_once __DIR__ . '/header.php';
?>

    <!-- Product Title Header Section -->
    <section class="product-profile-header-sec" style="background-color: #f1f3f5; padding: 2rem 0; border-bottom: 1px solid var(--border-gray);">
        <div class="container" style="display: flex; justify-content: space-between; align-items: center; gap: 2rem; flex-wrap: wrap;">
            
            <!-- Left Info -->
            <div style="flex: 1.5; min-width: 300px; text-align: left;">
                <h2 style="font-size: 1.6rem; color: #475569; font-weight: 700; line-height: 1.35; margin: 0;">
                    <?php echo htmlspecialchars($product['name']); ?>
                </h2>
            </div>

            <!-- Price Info -->
            <div style="flex: 0.8; min-width: 150px; text-align: center;">
                <span style="font-size: 2.2rem; font-weight: 800; color: var(--primary-blue); font-family: var(--font-heading); white-space: nowrap;">
                    <?php echo $product['price'] > 0 ? number_format($product['price'], 0, ',', '.') . ',00 €' : 'Inquire Price'; ?>
                </span>
            </div>

            <!-- Action Buttons -->
            <div style="display: flex; flex-direction: column; gap: 0.65rem; min-width: 210px;">
                <button type="button" class="btn-purchase-action" id="openPurchaseModalBtn">
                    Interest For Purchase
                </button>
                <button type="button" class="btn-rent-action" id="openRentModalBtn">
                    Interest For Rent
                </button>
            </div>

        </div>
    </section>

    <!-- Main Profile Content Body -->
    <section class="product-profile-body-sec" style="padding: 4rem 0; background-color: #ffffff;">
        <div class="container product-profile-grid">
            
            <!-- Left Column: Gallery -->
            <div>
                <!-- Large Active Image -->
                <div class="main-profile-img-wrap" style="position: relative; width: 100%; border: 1px solid var(--border-gray); border-radius: 4px; overflow: hidden; background-color: #ffffff; display: flex; align-items: center; justify-content: center; height: 420px; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                    <img id="mainActiveImg" src="<?php echo htmlspecialchars($product['photo_path']); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                    
                    <!-- Badge Overlay -->
                    <span class="product-badge badge-status <?php echo strtolower($product['status']); ?>" style="position: absolute; top: 1.5rem; right: 1.5rem; font-size: 0.85rem; padding: 0.4rem 1rem; border-radius: 999px;">
                        <?php echo ($product['status'] === 'New') ? 'NEW' : 'USED'; ?>
                    </span>
                </div>

                <!-- Gallery Thumbnails Grid -->
                <?php if (count($gallery) > 1): ?>
                    <div class="profile-thumbnails-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(100px, 1fr)); gap: 0.75rem; margin-top: 1rem;">
                        <?php foreach ($gallery as $index => $imgUrl): ?>
                            <div class="profile-thumb-card <?php echo $index === 0 ? 'active' : ''; ?>" 
                                 style="border: 1px solid var(--border-gray); border-radius: 4px; overflow: hidden; cursor: pointer; height: 75px; background: #ffffff; display: flex; align-items: center; justify-content: center; transition: all 0.2s;"
                                 onclick="switchActivePhoto(this, '<?php echo htmlspecialchars($imgUrl); ?>')">
                                <img src="<?php echo htmlspecialchars($imgUrl); ?>" alt="Product angle" style="max-width: 100%; max-height: 100%; object-fit: contain;">
                            </div>
                        <?php endforeach; ?>
                    </div>
                <?php endif; ?>
            </div>

            <!-- Right Column: Specs & Brochures -->
            <div>
                <!-- Specs Wrapper -->
                <div class="profile-specs-panel" style="background-color: #eaeaea; border-radius: 2px; padding: 2rem 2.5rem; text-align: left; box-shadow: 0 4px 15px rgba(0,0,0,0.02);">
                    
                    <ul class="profile-specs-list" style="list-style: none; padding: 0; margin: 0; display: flex; flex-direction: column; gap: 0.9rem; font-size: 0.95rem; color: #475569; font-weight: 500;">
                        <?php if (!empty($product['brand']) && $product['brand'] !== 'N/A'): ?>
                            <li>
                                <strong style="color: #64748b; font-weight: 600; min-width: 180px; display: inline-block;">Brand:</strong>
                                <span style="color: #1e293b; font-weight: 700;"><?php echo htmlspecialchars($product['brand'] ?? ''); ?></span>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($product['model']) && $product['model'] !== 'N/A'): ?>
                            <li>
                                <strong style="color: #64748b; font-weight: 600; min-width: 180px; display: inline-block;">Model:</strong>
                                <span style="color: #1e293b; font-weight: 700;"><?php echo htmlspecialchars($product['model'] ?? ''); ?></span>
                            </li>
                        <?php endif; ?>
                        <li>
                            <strong style="color: #64748b; font-weight: 600; min-width: 180px; display: inline-block;">Condition:</strong>
                            <span style="color: #1e293b; font-weight: 700;"><?php echo ($product['status'] === 'New') ? 'Brand New' : 'Used'; ?></span>
                        </li>
                        <li>
                            <strong style="color: #64748b; font-weight: 600; min-width: 180px; display: inline-block;">Type:</strong>
                            <span style="color: #1e293b; font-weight: 700;"><?php echo htmlspecialchars($product['category_name'] ?? ''); ?></span>
                        </li>
                        <?php if (!empty($product['lifting_capacity']) && $product['lifting_capacity'] !== 'N/A'): ?>
                            <li>
                                <strong style="color: #64748b; font-weight: 600; min-width: 180px; display: inline-block;">Capacity:</strong>
                                <span style="color: #1e293b; font-weight: 700;"><?php echo htmlspecialchars($product['lifting_capacity'] ?? ''); ?></span>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($product['lift_height']) && $product['lift_height'] !== 'N/A'): ?>
                            <li>
                                <strong style="color: #64748b; font-weight: 600; min-width: 180px; display: inline-block;">Lift Height Capacity:</strong>
                                <span style="color: #1e293b; font-weight: 700;"><?php echo htmlspecialchars($product['lift_height'] ?? ''); ?></span>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($product['attachment']) && $product['attachment'] !== 'N/A' && $product['attachment'] !== 'None'): ?>
                            <li>
                                <strong style="color: #64748b; font-weight: 600; min-width: 180px; display: inline-block;">Attachment:</strong>
                                <span style="color: #1e293b; font-weight: 700;"><?php echo htmlspecialchars($product['attachment'] ?? ''); ?></span>
                            </li>
                        <?php endif; ?>
                        <?php if (!empty($product['energy']) && $product['energy'] !== 'N/A'): ?>
                            <li>
                                <strong style="color: #64748b; font-weight: 600; min-width: 180px; display: inline-block;">Power:</strong>
                                <span style="color: #1e293b; font-weight: 700;"><?php echo htmlspecialchars($product['energy'] ?? ''); ?></span>
                            </li>
                        <?php endif; ?>
                    </ul>

                    <!-- Brochure Download (PDF Icon) -->
                    <div style="margin-top: 2.5rem; text-align: center; border-top: 1px solid rgba(0,0,0,0.06); padding-top: 1.5rem;">
                        <a href="generate-pdf.php?slug=<?php echo urlencode($product['slug']); ?>" target="_blank" style="display: inline-flex; align-items: center; gap: 0.5rem; color: #475569; font-weight: 700; text-decoration: none; font-size: 0.95rem; transition: color 0.2s;" onmouseover="this.style.color='#fec107'" onmouseout="this.style.color='#475569'">
                            <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 28px; height: 28px; color: #dc2626;">
                                <path fill-rule="evenodd" d="M5.625 1.5c-1.036 0-1.875.84-1.875 1.875v17.25c0 1.035.84 1.875 1.875 1.875h12.75c1.035 0 1.875-.84 1.875-1.875V12.75A3.75 3.75 0 0016.5 9h-1.875a1.875 1.875 0 01-1.875-1.875V3.375A3.75 3.75 0 009 1.5H5.625zM7.5 15a.75.75 0 01.75-.75h7.5a.75.75 0 010 1.5h-7.5A.75.75 0 017.5 15zm.75 2.25a.75.75 0 000 1.5h7.5a.75.75 0 000-1.5h-7.5z" clip-rule="evenodd" />
                                <path d="M12.971 1.816A5.23 5.23 0 0114.25 3v3.375c0 .621.504 1.125 1.125 1.125h3.375c.465 0 .901.18 1.184.494A1.5 1.5 0 0120 9.25V18a3 3 0 01-3 3H7a3 3 0 01-3-3V5a3 3 0 013-3h5.971z" />
                            </svg>
                            PDF
                        </a>
                    </div>
                </div>

            </div>

        <!-- Product Description Overview -->
        <?php if (!empty($product['description'])): ?>
            <style>
                .premium-product-desc-container {
                    margin-top: 3.5rem;
                    border-top: 1px solid var(--border-gray);
                    padding-top: 3rem;
                    text-align: left;
                }
                .premium-product-desc-container h3 {
                    font-size: 1.5rem;
                    color: #1e293b;
                    margin-bottom: 1.5rem;
                    font-weight: 700;
                    border-bottom: 2px solid var(--accent-orange);
                    display: inline-block;
                    padding-bottom: 0.5rem;
                    font-family: var(--font-heading);
                }
                .premium-product-desc-body {
                    color: #475569;
                    line-height: 1.8;
                    font-size: 1.05rem;
                }
                .premium-product-desc-body ul {
                    list-style-type: disc !important;
                    padding-left: 2rem !important;
                    margin: 1rem 0 !important;
                }
                .premium-product-desc-body li {
                    margin-bottom: 0.65rem !important;
                    color: #475569 !important;
                    list-style-type: disc !important;
                }
                .premium-product-desc-body li li {
                    list-style-type: circle !important;
                }
                .premium-product-desc-body img {
                    max-width: 100%;
                    height: auto;
                    display: block;
                    margin: 2rem auto;
                    border: 1px solid #e2e8f0;
                    border-radius: 4px;
                    box-shadow: 0 4px 12px rgba(0,0,0,0.03);
                    padding: 0.5rem;
                    background: #ffffff;
                }
                .premium-product-desc-body p {
                    margin-bottom: 1.25rem;
                }
                .premium-product-desc-body strong {
                    color: #1e293b;
                    font-weight: 700;
                }
            </style>
            <div class="container">
                <div class="premium-product-desc-container">
                    <h3>Product Overview</h3>
                    <div class="premium-product-desc-body">
                        <?php 
                        $desc = $product['description'];
                        if (preg_match('/<[a-z][^>]*>/i', $desc)) {
                            echo $desc;
                        } else {
                            echo nl2br(htmlspecialchars($desc));
                        }
                        ?>
                    </div>
                </div>
            </div>
        <?php endif; ?>
    </section>

    <!-- Related Products Grid Section -->
    <section class="related-products-section" style="padding: 4rem 0 6rem; background-color: var(--body-bg); border-top: 1px solid var(--border-gray);">
        <div class="container">
            
            <h3 style="font-size: 1.8rem; font-weight: 700; color: #1e293b; margin-bottom: 2.5rem; text-align: left;">
                Related Products
            </h3>

            <div class="products-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 2rem;">
                <?php foreach ($related as $rel): ?>
                    <article class="product-card" style="margin: 0; height: 100%; display: flex; flex-direction: column;">
                        
                        <div class="product-card-img-wrap">
                            <img src="<?php echo htmlspecialchars($rel['photo_path']); ?>" alt="<?php echo htmlspecialchars($rel['name']); ?>" class="product-card-img">
                            <span class="product-badge badge-brand"><?php echo htmlspecialchars($rel['brand']); ?></span>
                            <span class="product-badge badge-status <?php echo strtolower($rel['status']); ?>"><?php echo htmlspecialchars(strtoupper($rel['status'])); ?></span>
                        </div>

                        <div class="product-card-content" style="flex: 1; display: flex; flex-direction: column; justify-content: space-between;">
                            <div>
                                <h3 style="font-size: 1.05rem; font-weight: 700; margin-bottom: 1rem; line-height: 1.3; overflow-wrap: break-word;">
                                    <?php echo htmlspecialchars($rel['name']); ?>
                                </h3>
                                
                                <!-- Specs list -->
                                <div class="product-card-specs" style="margin-top: 0.5rem;">
                                    <?php if($rel['price'] > 0): ?>
                                        <div style="color: var(--accent-orange); font-weight: 800; font-size: 1.1rem; margin-top: 0.5rem;">
                                            <?php echo number_format($rel['price'], 0, ',', '.'); ?> €
                                        </div>
                                    <?php else: ?>
                                        <div style="color: var(--accent-orange); font-weight: 800; font-size: 1.1rem; margin-top: 0.5rem;">
                                            0,00 €
                                        </div>
                                    <?php endif; ?>
                                </div>
                            </div>

                            <!-- Actions -->
                            <div class="product-card-actions" style="margin-top: 1.5rem; display: block;">
                                <a href="product-detail.php?slug=<?php echo urlencode($rel['slug']); ?>" class="btn btn-primary view-details-premium-btn">
                                    View Details
                                </a>
                            </div>
                        </div>

                    </article>
                <?php endforeach; ?>
            </div>

        </div>
    </section>

    <!-- Interest For Purchase Inquiry Modal -->
    <div class="rental-modal-backdrop" id="purchaseInquiryModal">
        <div class="rental-modal-card" style="max-width: 500px; border-radius: 4px; max-height: 90vh; overflow-y: auto;">
            <div class="rental-modal-header" style="background-color: var(--primary-blue); color: #ffffff; padding: 1.25rem 2rem;">
                <h3 style="font-weight: 700; font-size: 1.2rem; margin: 0; color: #ffffff;">Interest For Purchase</h3>
                <button class="rental-modal-close-btn" id="closePurchaseModalBtn" style="color: #ffffff; opacity: 0.8;">&times;</button>
            </div>
            <div class="rental-modal-body" style="padding: 2rem 2.5rem; background: #ffffff;">
                
                <form id="purchaseInquiryForm" class="machine-inquiry-form">
                    <input type="hidden" name="action" value="submit_purchase_inquiry">
                    <input type="hidden" name="machine_id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="machine_name" value="<?php echo htmlspecialchars($product['name']); ?>">

                    <div class="form-group" style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-weight: 600; color: #475569; margin-bottom: 0.5rem; display: block; font-size: 0.9rem;">Full Name <span style="color:red;">*</span></label>
                        <input type="text" name="full_name" placeholder="Full Name *" required class="form-control" style="width:100%; box-sizing: border-box; height: 42px; border: 1px solid #cbd5e1; border-radius: 2px; padding: 0.5rem 1rem; font-size: 0.9rem;">
                    </div>

                    <div class="form-group" style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-weight: 600; color: #475569; margin-bottom: 0.5rem; display: block; font-size: 0.9rem;">Phone <span style="color:red;">*</span></label>
                        <input type="text" name="phone" placeholder="📞 Phone *" required class="form-control" style="width:100%; box-sizing: border-box; height: 42px; border: 1px solid #cbd5e1; border-radius: 2px; padding: 0.5rem 1rem; font-size: 0.9rem;">
                    </div>

                    <div class="form-group" style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-weight: 600; color: #475569; margin-bottom: 0.5rem; display: block; font-size: 0.9rem;">Entity <span style="color:red;">*</span></label>
                        <select name="entity" required class="form-control" style="width:100%; box-sizing: border-box; height: 42px; border: 1px solid #cbd5e1; border-radius: 2px; padding: 0 1rem; font-size: 0.9rem; background-color: #ffffff;">
                            <option value="" disabled selected>Entity</option>
                            <option value="Company">Company</option>
                            <option value="Individual">Individual</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem; text-align: left;">
                        <label style="font-weight: 600; color: #475569; margin-bottom: 0.5rem; display: block; font-size: 0.9rem;">Location <span style="color:red;">*</span></label>
                        <select name="location" required class="form-control" style="width:100%; box-sizing: border-box; height: 42px; border: 1px solid #cbd5e1; border-radius: 2px; padding: 0 1rem; font-size: 0.9rem; background-color: #ffffff;">
                            <option value="" disabled selected>Location</option>
                            <option value="Limassol">Limassol</option>
                            <option value="Nicosia">Nicosia</option>
                            <option value="Larnaca">Larnaca</option>
                            <option value="Paphos">Paphos</option>
                            <option value="Famagusta">Famagusta</option>
                        </select>
                    </div>

                    <div style="display: flex; gap: 0.75rem; align-items: start; margin-bottom: 1.75rem; text-align: left;">
                        <input type="checkbox" id="purchasePrivacyCheck" required style="margin-top: 3px; cursor: pointer;">
                        <label for="purchasePrivacyCheck" style="font-size: 0.85rem; color: #64748b; line-height: 1.45; cursor: pointer; user-select: none;">
                            By submitting this form, you are agreeing to all conditions of our Privacy Policy
                        </label>
                    </div>

                    <button type="submit" class="hz-filter-btn" style="display: block; margin: 0 auto; width: 140px; padding: 0.65rem 0; text-align: center; border: 1px solid var(--primary-blue); font-weight:700; background: #ffffff;">Submit</button>
                </form>
                
                <div id="purchaseSuccessMsg" style="display:none; text-align:center; padding: 2rem 0;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 54px; height: 54px; color: var(--primary-blue); margin: 0 auto 1.5rem;">
                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.74-5.25z" clip-rule="evenodd" />
                    </svg>
                    <h4 style="color: var(--primary-blue); font-weight: 800; font-size: 1.25rem; margin-bottom: 0.5rem;">Inquiry Submitted!</h4>
                    <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.5;">Thank you for your interest. Our representative will contact you shortly regarding the purchase.</p>
                </div>

            </div>
        </div>
    </div>

    <!-- Interest For Rent Inquiry Modal -->
    <div class="rental-modal-backdrop" id="rentInquiryModal">
        <div class="rental-modal-card" style="max-width: 500px; border-radius: 4px; max-height: 90vh; overflow-y: auto;">
            <div class="rental-modal-header" style="background-color: var(--primary-blue); color: #ffffff; padding: 1.25rem 2rem;">
                <h3 style="font-weight: 700; font-size: 1.2rem; margin: 0; color: #ffffff;">Interest For Rent</h3>
                <button class="rental-modal-close-btn" id="closeRentModalBtn" style="color: #ffffff; opacity: 0.8;">&times;</button>
            </div>
            <div class="rental-modal-body" style="padding: 2rem 2.5rem; background: #ffffff;">
                
                <form id="rentInquiryForm" class="machine-inquiry-form">
                    <input type="hidden" name="action" value="submit_rent_inquiry">
                    <input type="hidden" name="machine_id" value="<?php echo $product['id']; ?>">
                    <input type="hidden" name="machine_name" value="<?php echo htmlspecialchars($product['name']); ?>">

                    <div class="form-group" style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-weight: 600; color: #475569; margin-bottom: 0.5rem; display: block; font-size: 0.9rem;">Full Name <span style="color:red;">*</span></label>
                        <input type="text" name="full_name" placeholder="Full Name *" required class="form-control" style="width:100%; box-sizing: border-box; height: 42px; border: 1px solid #cbd5e1; border-radius: 2px; padding: 0.5rem 1rem; font-size: 0.9rem;">
                    </div>

                    <div class="form-group" style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-weight: 600; color: #475569; margin-bottom: 0.5rem; display: block; font-size: 0.9rem;">Phone <span style="color:red;">*</span></label>
                        <input type="text" name="phone" placeholder="📞 Phone *" required class="form-control" style="width:100%; box-sizing: border-box; height: 42px; border: 1px solid #cbd5e1; border-radius: 2px; padding: 0.5rem 1rem; font-size: 0.9rem;">
                    </div>

                    <div class="form-group" style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-weight: 600; color: #475569; margin-bottom: 0.5rem; display: block; font-size: 0.9rem;">Entity <span style="color:red;">*</span></label>
                        <select name="entity" required class="form-control" style="width:100%; box-sizing: border-box; height: 42px; border: 1px solid #cbd5e1; border-radius: 2px; padding: 0 1rem; font-size: 0.9rem; background-color: #ffffff;">
                            <option value="" disabled selected>Entity</option>
                            <option value="Company">Company</option>
                            <option value="Individual">Individual</option>
                        </select>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-weight: 600; color: #475569; margin-bottom: 0.5rem; display: block; font-size: 0.9rem;">From <span style="color:red;">*</span></label>
                        <input type="date" name="from_date" required class="form-control" style="width:100%; box-sizing: border-box; height: 42px; border: 1px solid #cbd5e1; border-radius: 2px; padding: 0.5rem 1rem; font-size: 0.9rem; background-color: #ffffff;">
                    </div>

                    <div class="form-group" style="margin-bottom: 1.25rem; text-align: left;">
                        <label style="font-weight: 600; color: #475569; margin-bottom: 0.5rem; display: block; font-size: 0.9rem;">Till <span style="color:red;">*</span></label>
                        <input type="date" name="till_date" required class="form-control" style="width:100%; box-sizing: border-box; height: 42px; border: 1px solid #cbd5e1; border-radius: 2px; padding: 0.5rem 1rem; font-size: 0.9rem; background-color: #ffffff;">
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem; text-align: left;">
                        <label style="font-weight: 600; color: #475569; margin-bottom: 0.5rem; display: block; font-size: 0.9rem;">Location <span style="color:red;">*</span></label>
                        <select name="location" required class="form-control" style="width:100%; box-sizing: border-box; height: 42px; border: 1px solid #cbd5e1; border-radius: 2px; padding: 0 1rem; font-size: 0.9rem; background-color: #ffffff;">
                            <option value="" disabled selected>Location</option>
                            <option value="Limassol">Limassol</option>
                            <option value="Nicosia">Nicosia</option>
                            <option value="Larnaca">Larnaca</option>
                            <option value="Paphos">Paphos</option>
                            <option value="Famagusta">Famagusta</option>
                        </select>
                    </div>

                    <div style="display: flex; gap: 0.75rem; align-items: start; margin-bottom: 1.75rem; text-align: left;">
                        <input type="checkbox" id="rentPrivacyCheck" required style="margin-top: 3px; cursor: pointer;">
                        <label for="rentPrivacyCheck" style="font-size: 0.85rem; color: #64748b; line-height: 1.45; cursor: pointer; user-select: none;">
                            By submitting this form, you are agreeing to all conditions of our Privacy Policy
                        </label>
                    </div>

                    <button type="submit" class="hz-filter-btn" style="display: block; margin: 0 auto; width: 140px; padding: 0.65rem 0; text-align: center; border: 1px solid var(--primary-blue); font-weight:700; background: #ffffff;">Submit</button>
                </form>
                
                <div id="rentSuccessMsg" style="display:none; text-align:center; padding: 2rem 0;">
                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 24 24" fill="currentColor" style="width: 54px; height: 54px; color: var(--primary-blue); margin: 0 auto 1.5rem;">
                        <path fill-rule="evenodd" d="M2.25 12c0-5.385 4.365-9.75 9.75-9.75s9.75 4.365 9.75 9.75-4.365 9.75-9.75 9.75S2.25 17.385 2.25 12zm13.36-1.814a.75.75 0 10-1.22-.872l-3.236 4.53L9.53 12.22a.75.75 0 00-1.06 1.06l2.25 2.25a.75.75 0 001.14-.094l3.74-5.25z" clip-rule="evenodd" />
                    </svg>
                    <h4 style="color: var(--primary-blue); font-weight: 800; font-size: 1.25rem; margin-bottom: 0.5rem;">Inquiry Submitted!</h4>
                    <p style="color: var(--text-muted); font-size: 0.9rem; line-height: 1.5;">Thank you for your interest. Our representative will contact you shortly regarding the rental period.</p>
                </div>

            </div>
        </div>
    </div>

    <!-- Thumbnail swap & modal script controllers -->
    <script>
        function switchActivePhoto(thumbElement, imgUrl) {
            // Update active state in thumbnail cards
            const siblings = thumbElement.parentNode.querySelectorAll('.profile-thumb-card');
            siblings.forEach(s => s.classList.remove('active'));
            thumbElement.classList.add('active');

            // Swap large image path
            const mainImg = document.getElementById('mainActiveImg');
            if (mainImg) {
                mainImg.src = imgUrl;
            }
        }

        document.addEventListener('DOMContentLoaded', () => {
            // Modals references
            const purchaseModal = document.getElementById('purchaseInquiryModal');
            const rentModal = document.getElementById('rentInquiryModal');
            
            // Buttons references
            const openPurchaseBtn = document.getElementById('openPurchaseModalBtn');
            const openRentBtn = document.getElementById('openRentModalBtn');
            const closePurchaseBtn = document.getElementById('closePurchaseModalBtn');
            const closeRentBtn = document.getElementById('closeRentModalBtn');

            // Forms & Success references
            const purchaseForm = document.getElementById('purchaseInquiryForm');
            const rentForm = document.getElementById('rentInquiryForm');
            const purchaseSuccess = document.getElementById('purchaseSuccessMsg');
            const rentSuccess = document.getElementById('rentSuccessMsg');

            // Open Purchase Modal
            if (openPurchaseBtn) {
                openPurchaseBtn.addEventListener('click', () => {
                    purchaseSuccess.style.display = 'none';
                    purchaseForm.style.display = 'block';
                    purchaseForm.reset();
                    purchaseModal.classList.add('open');
                });
            }

            // Close Purchase Modal
            if (closePurchaseBtn) {
                closePurchaseBtn.addEventListener('click', () => {
                    purchaseModal.classList.remove('open');
                });
            }

            // Open Rent Modal
            if (openRentBtn) {
                openRentBtn.addEventListener('click', () => {
                    rentSuccess.style.display = 'none';
                    rentForm.style.display = 'block';
                    rentForm.reset();
                    rentModal.classList.add('open');
                });
            }

            // Close Rent Modal
            if (closeRentBtn) {
                closeRentBtn.addEventListener('click', () => {
                    rentModal.classList.remove('open');
                });
            }

            // Close modals on clicking background
            window.addEventListener('click', (e) => {
                if (e.target === purchaseModal) {
                    purchaseModal.classList.remove('open');
                }
                if (e.target === rentModal) {
                    rentModal.classList.remove('open');
                }
            });

            // Handle Purchase Form submit
            if (purchaseForm) {
                purchaseForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const formData = new FormData(purchaseForm);
                    
                    fetch('product-detail.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            purchaseForm.style.display = 'none';
                            purchaseSuccess.style.display = 'block';
                        } else {
                            alert('Submission failed: ' + (data.error || 'Unknown error'));
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('An error occurred during submission.');
                    });
                });
            }

            // Handle Rent Form submit
            if (rentForm) {
                rentForm.addEventListener('submit', (e) => {
                    e.preventDefault();
                    const formData = new FormData(rentForm);
                    
                    fetch('product-detail.php', {
                        method: 'POST',
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            rentForm.style.display = 'none';
                            rentSuccess.style.display = 'block';
                        } else {
                            alert('Submission failed: ' + (data.error || 'Unknown error'));
                        }
                    })
                    .catch(err => {
                        console.error(err);
                        alert('An error occurred during submission.');
                    });
                });
            }
        });
    </script>

<?php
require_once __DIR__ . '/footer.php';
?>
