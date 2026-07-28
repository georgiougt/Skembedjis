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
        'name' => 'Tyres',
        'link' => 'tyres.php',
        'image_key' => 'cat_tyres',
        'alt' => 'Tyres'
    ],
    [
        'name' => 'Batteries & Chargers',
        'link' => 'batteries-chargers.php',
        'image_key' => 'cat_batteries',
        'alt' => 'Batteries & Chargers'
    ],
    [
        'name' => 'Cleaning Equipment',
        'link' => 'catalog.php?category=cleaning-equipment',
        'image_key' => 'cat_cleaning',
        'alt' => 'Cleaning Equipment'
    ],
    [
        'name' => 'Ramps',
        'link' => 'ramps.php',
        'image_key' => 'cat_ramps',
        'alt' => 'Ramps'
    ],
    [
        'name' => 'Miscellaneous',
        'link' => 'catalog.php?category=miscellaneous',
        'image_key' => 'cat_misc',
        'alt' => 'Miscellaneous'
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
    <section class="hyster-split-banner-sec" style="background-color: #2e2e2e; min-height: 380px; display: flex; color: #ffffff;">
        <!-- Left Forklift Image -->
        <div style="width: 32%; background-image: url('assets/forklift-eq.webp'); background-size: cover; background-position: center; min-height: 380px;" role="img" aria-label="Hyster Forklift"></div>
        
        <!-- Right Content Block -->
        <div style="width: 68%; background-color: #2e2e2e; display: flex; align-items: center; padding: 3rem 4rem; text-align: left;">
            <div style="display: flex; align-items: center; gap: 4rem; width: 100%;">
                <!-- Yellow Hyster Logo -->
                <div style="flex-shrink: 0;">
                    <img src="assets/hyster-yellow-logo.webp" alt="Hyster - Strong Partners. Tough Trucks." style="max-height: 220px; width: auto; display: block;">
                </div>
                
                <!-- Text Column -->
                <div style="flex: 1; display: flex; flex-direction: column;">
                    <div style="font-size: 1.15rem; font-weight: 300; color: #e2e8f0; letter-spacing: 0.02em; margin-bottom: 0.75rem; font-family: inherit;">
                        Explore Our Hyster Products
                    </div>
                    
                    <!-- Top Line -->
                    <div style="width: 100%; height: 1px; background-color: rgba(255, 255, 255, 0.25);"></div>
                    
                    <!-- Headline -->
                    <h3 style="font-size: 1.7rem; font-weight: 400; color: #ffffff; text-transform: uppercase; line-height: 1.45; letter-spacing: 0.04em; margin: 1.75rem 0; font-family: inherit;">
                        DISTINCTLY HYSTER,<br>BUILT FOR YOU
                    </h3>
                    
                    <!-- Bottom Line -->
                    <div style="width: 100%; height: 1px; background-color: rgba(255, 255, 255, 0.25);"></div>
                </div>
            </div>
        </div>
    </section>

<?php
require_once __DIR__ . '/footer.php';
?>
