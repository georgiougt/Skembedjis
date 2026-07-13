<?php
// products.php
// Recreated Products Landing Page (Our Equipment Catalog Main Grid)

require_once __DIR__ . '/db.php';

// Fetch title setting
$products_title = get_section('products', 'products_page_title', 'Our Equipment', '');

// Fetch Hyster banner texts
$hyster_sub = get_section('products', 'hyster_banner_sub', 'Explore Our Hyster Products', '');
$hyster_title = get_section('products', 'hyster_banner_title', 'DISTINCTLY HYSTER, BUILT FOR YOU', '');

// Get images
$badge_iso_img = get_image_path('badge_iso', 'ISO 9001 Badge');
$badge_iqnet_img = get_image_path('badge_iqnet', 'IQNET Badge');
$badge_itssar_img = get_image_path('badge_itssar_accredited', 'ITSSAR Accredited Training Provider Badge');
$hyster_left_img = get_image_path('hyster_banner_forklift', 'Hyster Forklift Banner Left');

// Grid categories list
$categories = [
    [
        'name' => 'Forklifts',
        'link' => 'forklifts.php',
        'image_key' => 'cat_forklifts',
        'alt' => 'Forklifts'
    ],
    [
        'name' => 'Reach Trucks',
        'link' => 'reach-trucks.php',
        'image_key' => 'cat_reach_trucks',
        'alt' => 'Reach Trucks'
    ],
    [
        'name' => 'Stackers',
        'link' => 'stackers.php',
        'image_key' => 'cat_stackers',
        'alt' => 'Stackers'
    ],
    [
        'name' => 'Pallet Trucks',
        'link' => 'pallet-trucks.php',
        'image_key' => 'cat_pallet_trucks',
        'alt' => 'Pallet Trucks'
    ],
    [
        'name' => 'Order Pickers',
        'link' => 'order-pickers.php',
        'image_key' => 'cat_order_pickers',
        'alt' => 'Order Pickers'
    ],
    [
        'name' => 'VNA',
        'link' => 'vna.php',
        'image_key' => 'cat_vna',
        'alt' => 'VNA'
    ],
    [
        'name' => 'Truck Mounted Forklifts',
        'link' => 'truck-mounted.php',
        'image_key' => 'cat_truck_mounted',
        'alt' => 'Truck Mounted Forklifts'
    ],
    [
        'name' => 'Tyres',
        'link' => 'tyres.php',
        'image_key' => 'cat_tyres',
        'alt' => 'Tyres'
    ],
    [
        'name' => 'Handling Equipment',
        'link' => 'handling.php',
        'image_key' => 'cat_handling',
        'alt' => 'Handling Equipment'
    ],
    [
        'name' => 'Attachments',
        'link' => 'attachments.php',
        'image_key' => 'cat_attachments',
        'alt' => 'Attachments'
    ],
    [
        'name' => 'Batteries & Chargers',
        'link' => 'batteries-chargers.php',
        'image_key' => 'cat_batteries',
        'alt' => 'Batteries & Chargers'
    ],
    [
        'name' => 'Ramps',
        'link' => 'ramps.php',
        'image_key' => 'cat_ramps',
        'alt' => 'Ramps'
    ]
];

$current_page = 'products.php';
require_once __DIR__ . '/header.php';
?>

    <!-- Page Title Header Block -->
    <section class="page-header-block">
        <div class="container">
            <div class="divider-line centered"></div>
            <h2><?php echo htmlspecialchars($products_title['title']); ?></h2>
        </div>
    </section>

    <!-- Categories Grid Section -->
    <section class="products-grid-section" style="padding: 2rem 0 5rem; background-color: var(--body-bg);">
        <div class="container">
            <div class="equipment-categories-grid">
                <?php foreach ($categories as $cat): 
                    $cat_img = get_image_path($cat['image_key'], $cat['alt']);
                ?>
                    <div class="equipment-cat-card">
                        <a href="<?php echo htmlspecialchars($cat['link']); ?>" class="equipment-cat-img-link">
                            <img src="<?php echo htmlspecialchars($cat_img); ?>" alt="<?php echo htmlspecialchars($cat['alt']); ?>" class="equipment-cat-img">
                        </a>
                        <a href="<?php echo htmlspecialchars($cat['link']); ?>" class="equipment-cat-title-link">
                            <?php echo htmlspecialchars($cat['name']); ?>
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Accreditation Badges Section -->
    <section class="accreditation-badges-sec" style="padding: 3rem 0; background-color: #ffffff; border-top: 1px solid var(--border-gray); border-bottom: 1px solid var(--border-gray);">
        <div class="container" style="display: flex; justify-content: center; align-items: center; gap: 5rem; flex-wrap: wrap;">
            <img src="<?php echo htmlspecialchars($badge_iso_img); ?>" alt="ISO 9001 Certificate Badge" class="accreditation-badge-circular">
            <img src="<?php echo htmlspecialchars($badge_iqnet_img); ?>" alt="IQNET Badge" class="accreditation-badge-circular">
            <img src="<?php echo htmlspecialchars($badge_itssar_img); ?>" alt="ITSSAR Accredited Badge" class="accreditation-badge-circular">
        </div>
    </section>

    <!-- Hyster Banner Split Block -->
    <section class="hyster-split-banner-sec" style="background-color: #1e1e1e; min-height: 400px; display: flex; color: #ffffff;">
        <div style="flex: 1; background-image: url('<?php echo htmlspecialchars($hyster_left_img); ?>'); background-size: cover; background-position: center; min-height: 400px;" role="img" aria-label="Hyster Forklift"></div>
        <div style="flex: 1.2; background-color: #262626; display: flex; flex-direction: column; justify-content: center; padding: 4rem 5rem; text-align: left;">
            <div style="max-width: 500px;">
                <!-- Hyster Logo Box -->
                <div style="border: 4px solid #fec107; display: inline-flex; flex-direction: column; align-items: center; padding: 0.5rem 1rem; margin-bottom: 2rem;">
                    <span style="font-family: 'Arial Black', sans-serif; font-size: 2.2rem; font-weight: 900; color: #ffffff; line-height: 0.95; letter-spacing: -1px; text-transform: uppercase;">Hyster</span>
                    <span style="font-size: 0.52rem; font-weight: 700; color: #fec107; margin-top: 0.2rem; text-transform: uppercase; letter-spacing: 0.05em; font-family: inherit;">Strong Partners. Tough Trucks.™</span>
                </div>

                <p style="color: #a3a3a3; font-size: 0.95rem; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem; font-weight: 600;"><?php echo htmlspecialchars($hyster_sub['content']); ?></p>
                <div style="width: 100%; height: 1px; background-color: rgba(255, 255, 255, 0.15); margin-bottom: 1.5rem;"></div>
                <h3 style="font-size: 1.8rem; font-weight: 600; color: #ffffff; line-height: 1.35; letter-spacing: 0.02em;"><?php echo htmlspecialchars($hyster_title['content']); ?></h3>
            </div>
        </div>
    </section>

<?php
require_once __DIR__ . '/footer.php';
?>
