<?php
// sell-machine.php
// Recreated Sell Your Machine Page & Form

require_once __DIR__ . '/db.php';

$success_msg = '';
$error_msg = '';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = trim($_POST['full_name'] ?? '');
    $phone = trim($_POST['phone'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $model = trim($_POST['model'] ?? '');
    $power = trim($_POST['power'] ?? '');
    $serial_number = trim($_POST['serial_number'] ?? '');
    $condition_details = trim($_POST['condition_details'] ?? '');
    $location = trim($_POST['location'] ?? '');

    $photo_path = '';

    if (empty($full_name) || empty($phone) || empty($email) || empty($model) || empty($location)) {
        $error_msg = 'Please fill in all required fields marked with an asterisk (*).';
    } else {
        // Handle image upload
        if (isset($_FILES['machine_photo']) && $_FILES['machine_photo']['error'] === UPLOAD_ERR_OK) {
            $fileTmpPath = $_FILES['machine_photo']['tmp_name'];
            $fileName = $_FILES['machine_photo']['name'];
            $fileSize = $_FILES['machine_photo']['size'];
            $fileType = $_FILES['machine_photo']['type'];
            $fileNameCmps = explode(".", $fileName);
            $fileExtension = strtolower(end($fileNameCmps));

            $allowedExtensions = ['jpg', 'jpeg', 'png', 'webp'];
            if (in_array($fileExtension, $allowedExtensions)) {
                $uploadDir = __DIR__ . '/uploads/sell_machine/';
                if (!is_dir($uploadDir)) {
                    mkdir($uploadDir, 0777, true);
                }

                $newFileName = md5(time() . $fileName) . '.' . $fileExtension;
                $dest_path = $uploadDir . $newFileName;

                if (move_uploaded_file($fileTmpPath, $dest_path)) {
                    $photo_path = 'uploads/sell_machine/' . $newFileName;
                } else {
                    $error_msg = 'There was an error saving the uploaded image file.';
                }
            } else {
                $error_msg = 'Invalid file type. Only JPG, JPEG, PNG, and WEBP machine photos are allowed.';
            }
        }

        if (empty($error_msg)) {
            // Save to SQLite
            try {
                $stmt = $db->prepare("INSERT INTO sell_machine_requests (
                    full_name, phone, email, model, power, serial_number, photo_path, condition_details, location
                ) VALUES (
                    :full_name, :phone, :email, :model, :power, :serial_number, :photo_path, :condition_details, :location
                )");
                
                $stmt->execute([
                    ':full_name' => $full_name,
                    ':phone' => $phone,
                    ':email' => $email,
                    ':model' => $model,
                    ':power' => $power,
                    ':serial_number' => $serial_number,
                    ':photo_path' => $photo_path,
                    ':condition_details' => $condition_details,
                    ':location' => $location
                ]);
                $success_msg = 'Your sale inquiry has been submitted successfully! Our valuation experts will contact you shortly.';
            } catch (PDOException $e) {
                $error_msg = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

// Fetch sections
$page_title = get_section('sell_machine', 'sell_machine_title', 'Sell Your Machine', '');
$page_subtitle = get_section('sell_machine', 'sell_machine_subtitle', '', 'Submit your material handling equipment details and upload a photo to receive a custom market valuation from our sales team.');
$bannerImg = get_image_path('sell_machine_banner', 'Sell Machine Banner');

require_once __DIR__ . '/header.php';
?>

    <!-- Page Title Header Block -->
    <section class="page-header-block">
        <div class="container">
            <div class="divider-line centered"></div>
            <h2><?php echo htmlspecialchars($page_title['title']); ?></h2>
            <p style="text-align: center; color: var(--text-muted); font-size: 0.95rem; margin-top: 0.5rem; max-width: 700px; margin-left: auto; margin-right: auto; line-height: 1.6;">
                <?php echo htmlspecialchars($page_subtitle['content']); ?>
            </p>
        </div>
    </section>

    <!-- Success / Error Feedback Alert Bar -->
    <?php if (!empty($success_msg)): ?>
        <div class="container" style="margin-top: 2rem; max-width: 800px;">
            <div style="background-color: #ecfdf5; border: 1px solid #a7f3d0; color: #065f46; padding: 1rem 1.5rem; border-radius: 6px; font-weight: 600;">
                ✓ <?php echo htmlspecialchars($success_msg); ?>
            </div>
        </div>
    <?php endif; ?>
    <?php if (!empty($error_msg)): ?>
        <div class="container" style="margin-top: 2rem; max-width: 800px;">
            <div style="background-color: #fef2f2; border: 1px solid #fecaca; color: #991b1b; padding: 1rem 1.5rem; border-radius: 6px; font-weight: 600;">
                ⚠️ <?php echo htmlspecialchars($error_msg); ?>
            </div>
        </div>
    <?php endif; ?>

    <!-- Form Section -->
    <section class="container">
        <div class="form-container-card">
            <form action="sell-machine.php" method="POST" enctype="multipart/form-data">
                
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

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label" for="email">Email *</label>
                    <input type="email" name="email" id="email" class="form-control" placeholder="name@company.com" required>
                </div>

                <div class="form-row-2col">
                    <div class="form-group">
                        <label class="form-label" for="model">Manufacturer & Model *</label>
                        <input type="text" name="model" id="model" class="form-control" placeholder="e.g. Hyster - J1.6XNT" required>
                    </div>
                    <div class="form-group">
                        <label class="form-label" for="power">Power</label>
                        <select name="power" id="power" class="form-control">
                            <option value="Diesel">Diesel</option>
                            <option value="LPG">LPG</option>
                            <option value="Gasoline">Gasoline</option>
                            <option value="Electric">Electric</option>
                        </select>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label" for="serial_number">Serial Number</label>
                    <input type="text" name="serial_number" id="serial_number" class="form-control" placeholder="Enter machine serial number">
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label">Machine Photo *</label>
                    <div class="file-upload-wrapper" onclick="document.getElementById('machine_photo').click();">
                        <span class="file-upload-text">Click or drag a file to this area to upload machine photo.</span>
                        <input type="file" name="machine_photo" id="machine_photo" required style="margin: 0 auto; display: block;" accept="image/*" onchange="event.stopPropagation();">
                    </div>
                </div>

                <div class="form-group" style="margin-bottom: 1.5rem;">
                    <label class="form-label" for="condition_details">Condition / Any details we need to know?</label>
                    <textarea name="condition_details" id="condition_details" class="form-control" style="min-height: 100px;" placeholder="Details about working conditions, mast type, operating hours, tyres..."></textarea>
                </div>

                <div class="form-group" style="margin-bottom: 2rem;">
                    <label class="form-label" for="location">Location *</label>
                    <select name="location" id="location" class="form-control" required>
                        <option value="Limassol">Limassol</option>
                        <option value="Nicosia">Nicosia</option>
                        <option value="Larnaca">Larnaca</option>
                        <option value="Paphos">Paphos</option>
                        <option value="Famagusta">Famagusta</option>
                    </select>
                </div>

                <div style="margin-bottom: 2rem; display: flex; align-items: flex-start; gap: 0.75rem;">
                    <input type="checkbox" id="privacy_policy" required style="margin-top: 0.25rem; width: 16px; height: 16px; cursor: pointer;">
                    <label for="privacy_policy" style="font-size: 0.85rem; color: #475569; line-height: 1.4; cursor: pointer;">
                        By submitting this form, you are agreeing to all conditions of our <a href="privacy.php" target="_blank" style="color: var(--accent-orange); font-weight: 600;">Privacy Policy</a>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary" style="width: 100%; padding: 1rem 0;">Submit Valuation Request</button>
            </form>
        </div>
    </section>

<?php
require_once __DIR__ . '/footer.php';
?>
