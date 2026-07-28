<?php
// spare-parts.php
// Recreated Spare Parts Page with Request a Part Modal Form

require_once __DIR__ . '/db.php';

$success_msg = '';
$error_msg = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $entity = trim($_POST['entity'] ?? '');
    $part_description = trim($_POST['part_description'] ?? '');
    $quantity = intval($_POST['quantity'] ?? 1);
    $brand = trim($_POST['brand'] ?? '');
    $model = trim($_POST['model'] ?? '');
    $serial_number = trim($_POST['serial_number'] ?? '');
    $location = trim($_POST['location'] ?? '');

    if (empty($full_name) || empty($phone) || empty($entity) || empty($part_description) || $quantity <= 0 || empty($brand) || empty($model) || empty($location)) {
        $error_msg = 'Please fill in all required fields marked with an asterisk (*).';
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO part_requests (
                full_name, phone, entity, part_description, quantity, brand, model, serial_number, location
            ) VALUES (
                :full_name, :phone, :entity, :part_description, :quantity, :brand, :model, :serial_number, :location
            )");
            
            $stmt->execute([
                ':full_name' => $full_name,
                ':phone' => $phone,
                ':entity' => $entity,
                ':part_description' => $part_description,
                ':quantity' => $quantity,
                ':brand' => $brand,
                ':model' => $model,
                ':serial_number' => $serial_number,
                ':location' => $location
            ]);
            $success_msg = 'Your part request has been submitted successfully! Our parts department will contact you shortly.';
        } catch (PDOException $e) {
            $error_msg = 'Database error: ' . $e->getMessage();
        }
    }
}

// Fetch sections
$parts_title = get_section('spare_parts', 'parts_title', 'Spare Parts', '');
$banner_title = get_section('spare_parts', 'parts_banner_title', 'Not in Stock?', '');
$banner_sub = get_section('spare_parts', 'parts_banner_subtitle', 'No Problem!', '');
$cta_title = get_section('spare_parts', 'parts_cta_title', 'Does your forklift require a part ?', '');
$cta_body = get_section('spare_parts', 'parts_cta_body', '', 'Then we probably have all you need in stock at our warehouses in Limassol or Nicosia. At Y. Skembedjis and Sons we do our best to keep the parts for all trucks and equipment we sell in stock, in our efforts to minimize the downtime for our client trucks. We have the largest selection of New and Used Forklift Parts warehouse in Cyprus ensuring that we can accommodate your needs for a repair in the most economical way without jeopardizing the quality of the products.');
$cta_btn = get_section('spare_parts', 'parts_cta_btn_text', '', 'Request A Part');
$store_title = get_section('spare_parts', 'parts_store_title', 'Forklift Spare Parts Store', '');
$store_details = get_section('spare_parts', 'parts_store_details', '', "1 Agoras Street, Ypsonas Industrial Area\n3056 Limassol – Cyprus\n\nPostal Address: P.O.Box 53312,\n3302 Limassol – Cyprus\n\n+357 25 712 265 +357 25 710 413\nforkliftparts@skembedjis.com");

// Fetch image
$partsCollage = 'assets/spareparts-banner.webp';

require_once __DIR__ . '/header.php';
?>

    <!-- Page Title Header Block -->
    <section class="page-header-block">
        <div class="container">
            <div class="divider-line centered"></div>
            <h2><?php echo htmlspecialchars($parts_title['title']); ?></h2>
        </div>
    </section>

    <!-- Split Banner Section -->
    <section class="parts-split-banner">
        <div class="parts-left-img" style="background-image: url('<?php echo htmlspecialchars($partsCollage); ?>');" role="img" aria-label="Spare Parts Display"></div>
        <div class="parts-right-text">
            <h3><?php echo htmlspecialchars($banner_title['title']); ?></h3>
            <p><?php echo htmlspecialchars($banner_sub['title']); ?></p>
        </div>
    </section>

    <!-- Success / Error Feedback Alert Bar -->
    <?php if (!empty($success_msg)): ?>
        <div class="container" style="margin-top: 2rem;">
            <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 1rem 1.5rem; border-radius: 6px; font-weight: 600;">
                ✓ <?php echo htmlspecialchars($success_msg); ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if (!empty($error_msg)): ?>
        <div class="container" style="margin-top: 2rem;">
            <div style="background-color: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 1rem 1.5rem; border-radius: 6px; font-weight: 600;">
                ⚠️ <?php echo htmlspecialchars($error_msg); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- CTA Description Section -->
    <section class="parts-description-sec">
        <div class="container">
            <h3><?php echo htmlspecialchars($cta_title['title']); ?></h3>
            <p><?php echo htmlspecialchars($cta_body['content']); ?></p>
            <button class="btn btn-blue-outline" id="openPartFormBtn"><?php echo htmlspecialchars($cta_btn['content']); ?></button>
        </div>
    </section>

    <!-- Store Address Details Section -->
    <section class="parts-contact-sec">
        <div class="container parts-contact-grid">
            <div class="parts-contact-left">
                <h3><?php echo htmlspecialchars($store_title['title']); ?></h3>
            </div>
            
            <div class="parts-contact-divider"></div>
            
            <div class="parts-contact-right">
                <?php echo nl2br(htmlspecialchars($store_details['content'])); ?>
            </div>
        </div>
    </section>

    <!-- Request a Part Form Modal Backdrop -->
    <div class="rental-modal-backdrop" id="partRequestModal">
        <div class="rental-modal-card">
            <div class="rental-modal-header">
                <h3>Request Forklift Spare Part</h3>
                <button class="rental-modal-close-btn" id="closePartModalBtn">&times;</button>
            </div>
            
            <div class="rental-modal-body">
                <form action="spare-parts.php" method="POST">
                    
                    <div class="form-row-2col">
                        <div class="form-group">
                            <label class="form-label" for="full_name">Full Name *</label>
                            <input type="text" name="full_name" id="full_name" class="form-control" placeholder="Enter full name" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="phone">Phone *</label>
                            <input type="text" name="phone" id="phone" class="form-control" placeholder="Enter contact number" required>
                        </div>
                    </div>

                    <div class="form-row-2col">
                        <div class="form-group">
                            <label class="form-label" for="entity">Entity *</label>
                            <select name="entity" id="entity" class="form-control" required>
                                <option value="Company">Company</option>
                                <option value="Individual">Individual</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="location">Location *</label>
                            <select name="location" id="location" class="form-control" required>
                                <option value="Limassol">Limassol</option>
                                <option value="Nicosia">Nicosia</option>
                                <option value="Larnaca">Larnaca</option>
                                <option value="Paphos">Paphos</option>
                                <option value="Famagusta">Famagusta</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row-2col">
                        <div class="form-group">
                            <label class="form-label" for="brand">Brand * (Brand of related truck)</label>
                            <input type="text" name="brand" id="brand" class="form-control" placeholder="e.g. Hyster" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="model">Model * (Model of related truck)</label>
                            <input type="text" name="model" id="model" class="form-control" placeholder="e.g. H3.0XT" required>
                        </div>
                    </div>

                    <div class="form-row-2col">
                        <div class="form-group">
                            <label class="form-label" for="quantity">Quantity *</label>
                            <input type="number" name="quantity" id="quantity" class="form-control" min="1" value="1" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="serial_number">Serial Number (Serial number of related truck)</label>
                            <input type="text" name="serial_number" id="serial_number" class="form-control" placeholder="e.g. A257F01548Z">
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 2rem;">
                        <label class="form-label" for="part_description">Part description *</label>
                        <textarea name="part_description" id="part_description" class="form-control" style="min-height: 100px;" placeholder="Please describe the part(s) you need in detail..." required></textarea>
                    </div>

                    <div style="margin-bottom: 2rem; display: flex; align-items: flex-start; gap: 0.75rem;">
                        <input type="checkbox" id="privacy_policy" required style="margin-top: 0.25rem; width: 16px; height: 16px; cursor: pointer;">
                        <label for="privacy_policy" style="font-size: 0.85rem; color: #475569; line-height: 1.4; cursor: pointer;">
                            By submitting this form, you are agreeing to all conditions of our <a href="privacy.php" target="_blank" style="color: var(--accent-orange); font-weight: 600;">Privacy Policy</a>
                        </label>
                    </div>

                    <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem 0;">Submit Request</button>
                </form>
            </div>
        </div>
    </div>

    <!-- Modal scripting -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const modal = document.getElementById('partRequestModal');
            const openBtn = document.getElementById('openPartFormBtn');
            const closeBtn = document.getElementById('closePartModalBtn');

            if (openBtn && modal) {
                openBtn.addEventListener('click', () => {
                    modal.classList.add('open');
                });
            }

            if (closeBtn && modal) {
                closeBtn.addEventListener('click', () => {
                    modal.classList.remove('open');
                });
            }

            // Close on clicking outside form card wrapper
            window.addEventListener('click', (e) => {
                if (e.target === modal) {
                    modal.classList.remove('open');
                }
            });
        });
    </script>

<?php
require_once __DIR__ . '/footer.php';
?>
