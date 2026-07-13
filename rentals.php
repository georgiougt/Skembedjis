<?php
// rentals.php
// Recreated Rentals Page & Modal Request Form

require_once __DIR__ . '/db.php';

$success_msg = '';
$error_msg = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $entity = trim($_POST['entity'] ?? '');
    $condition = trim($_POST['condition'] ?? '');
    $from_date = trim($_POST['from_date'] ?? '');
    $till_date = trim($_POST['till_date'] ?? '');
    $full_address = trim($_POST['full_address'] ?? '');
    $location = trim($_POST['location'] ?? '');
    $max_weight = trim($_POST['max_weight'] ?? '');
    $max_height = trim($_POST['max_height'] ?? '');
    $forks_length = trim($_POST['forks_length'] ?? '');
    $application_work = trim($_POST['application_work'] ?? '');
    $specifications = trim($_POST['specifications'] ?? '');

    if (empty($full_name) || empty($phone) || empty($condition) || empty($from_date) || empty($till_date) || empty($full_address) || empty($location)) {
        $error_msg = 'Please fill in all required fields marked with an asterisk (*).';
    } else {
        try {
            $stmt = $db->prepare("INSERT INTO rental_requests (
                full_name, phone, entity, condition, from_date, till_date, full_address, location, max_weight, max_height, forks_length, application_work, specifications
            ) VALUES (
                :full_name, :phone, :entity, :condition, :from_date, :till_date, :full_address, :location, :max_weight, :max_height, :forks_length, :application_work, :specifications
            )");
            
            $stmt->execute([
                ':full_name' => $full_name,
                ':phone' => $phone,
                ':entity' => $entity,
                ':condition' => $condition,
                ':from_date' => $from_date,
                ':till_date' => $till_date,
                ':full_address' => $full_address,
                ':location' => $location,
                ':max_weight' => $max_weight,
                ':max_height' => $max_height,
                ':forks_length' => $forks_length,
                ':application_work' => $application_work,
                ':specifications' => $specifications
            ]);
            $success_msg = 'Your rental inquiry has been submitted successfully! Our team will contact you shortly.';
        } catch (PDOException $e) {
            $error_msg = 'Database error: ' . $e->getMessage();
        }
    }
}

// Fetch sections
$rentals_title = get_section('rentals', 'rentals_title', 'Rentals', '');
$banner_text = get_section('rentals', 'rentals_banner_text', '', 'Does your business experience peak periods which places extra demands on both equipment and manpower?');
$col1_heading = get_section('rentals', 'rentals_col1_heading', 'Remember – you don’t have to own a truck to use it!', '');
$col1_body = get_section('rentals', 'rentals_col1_body', '', 'Y. Skembedjis & Sons Ltd can satisfy your rental needs in Cyprus...');
$col1_commit = get_section('rentals', 'rentals_col1_commit', '', 'We commit to: A rapid response...');
$col2_body1 = get_section('rentals', 'rentals_col2_body1', '', 'We can provide you with the right truck...');
$col2_body2 = get_section('rentals', 'rentals_col2_body2', '', 'Keep in mind that we have the ability...');
$col2_body3 = get_section('rentals', 'rentals_col2_body3', '', 'Please don’t hesitate to ask...');
$cta_title = get_section('rentals', 'rentals_cta_title', 'Rent your machine online', '');

// Fetch banner image
$bannerBg = get_image_path('rentals_banner_bg', 'Rentals Banner');

require_once __DIR__ . '/header.php';
?>

    <!-- Page Title Header Block -->
    <section class="page-header-block">
        <div class="container">
            <div class="divider-line centered"></div>
            <h2><?php echo htmlspecialchars($rentals_title['title']); ?></h2>
        </div>
    </section>

    <!-- Split Banner Section -->
    <section class="parts-split-banner">
        <div class="parts-left-img" style="background-image: url('<?php echo htmlspecialchars($bannerBg); ?>');" role="img" aria-label="Forklift Rentals Banner"></div>
        <div class="parts-right-text">
            <h3>Rentals</h3>
            <p><?php echo htmlspecialchars($banner_text['title']); ?></p>
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

    <!-- Two Column Description Section -->
    <section class="rentals-description-sec">
        <div class="container rentals-grid">
            <!-- Left Column -->
            <div class="rentals-col">
                <h3><?php echo htmlspecialchars($col1_heading['title']); ?></h3>
                <p><?php echo htmlspecialchars($col1_body['content']); ?></p>
                
                <div class="rentals-commit-block">
                    <strong>We commit to:</strong>
                    <p><?php echo htmlspecialchars($col1_commit['content']); ?></p>
                </div>
            </div>
            
            <!-- Right Column -->
            <div class="rentals-col">
                <p><?php echo htmlspecialchars($col2_body1['content']); ?></p>
                <p><?php echo htmlspecialchars($col2_body2['content']); ?></p>
                <p><?php echo htmlspecialchars($col2_body3['content']); ?></p>
            </div>
        </div>
    </section>

    <!-- CTA Online Section -->
    <section class="rentals-cta-online-sec">
        <div class="container">
            <h3><?php echo htmlspecialchars($cta_title['title']); ?></h3>
            <p><?php echo htmlspecialchars($banner_text['title']); ?></p>
            <button class="btn btn-blue-outline" id="openRentalFormBtn">Rent Now</button>
        </div>
    </section>

    <!-- Rental Form Modal Backdrop overlay -->
    <div class="rental-modal-backdrop" id="rentalModal">
        <div class="rental-modal-card">
            <div class="rental-modal-header">
                <h3>Inquire Forklift Rental</h3>
                <button class="rental-modal-close-btn" id="closeRentalFormBtn">&times;</button>
            </div>
            
            <div class="rental-modal-body">
                <form action="rentals.php" method="POST">
                    
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
                            <label class="form-label" for="entity">Entity</label>
                            <select name="entity" id="entity" class="form-control">
                                <option value="Company">Company</option>
                                <option value="Individual">Individual</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="condition">Condition *</label>
                            <select name="condition" id="condition" class="form-control" required>
                                <option value="New">New</option>
                                <option value="Used">Used / Pre-owned</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row-2col">
                        <div class="form-group">
                            <label class="form-label" for="from_date">From *</label>
                            <input type="date" name="from_date" id="from_date" class="form-control" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="till_date">Till *</label>
                            <input type="date" name="till_date" id="till_date" class="form-control" required>
                        </div>
                    </div>

                    <div class="form-row-2col">
                        <div class="form-group">
                            <label class="form-label" for="full_address">Full Address *</label>
                            <input type="text" name="full_address" id="full_address" class="form-control" placeholder="Delivery street name & door number" required>
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
                            <label class="form-label" for="max_weight">Max Lifting Weight (kg) *</label>
                            <input type="text" name="max_weight" id="max_weight" class="form-control" placeholder="e.g. 2500 kg" required>
                        </div>
                        <div class="form-group">
                            <label class="form-label" for="max_height">Max Lifting Height (mm) *</label>
                            <input type="text" name="max_height" id="max_height" class="form-control" placeholder="e.g. 4500 mm" required>
                        </div>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label class="form-label" for="forks_length">Forks length *</label>
                        <input type="text" name="forks_length" id="forks_length" class="form-control" placeholder="e.g. 1200 mm" required>
                    </div>

                    <div class="form-group" style="margin-bottom: 1.5rem;">
                        <label class="form-label" for="application_work">Application of work</label>
                        <textarea name="application_work" id="application_work" class="form-control" style="min-height: 80px;" placeholder="Describe what operations the forklift will perform..."></textarea>
                    </div>

                    <div class="form-group" style="margin-bottom: 2rem;">
                        <label class="form-label" for="specifications">Additional specifications</label>
                        <textarea name="specifications" id="specifications" class="form-control" style="min-height: 80px;" placeholder="Any custom details (e.g. side shift, tyre type, mast restrictions)"></textarea>
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
        const modal = document.getElementById('rentalModal');
        const openBtn = document.getElementById('openRentalFormBtn');
        const closeBtn = document.getElementById('closeRentalFormBtn');

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
    </script>

<?php
require_once __DIR__ . '/footer.php';
?>
