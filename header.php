<?php
// header.php
// Modular Header Navigation

require_once __DIR__ . '/db.php';
$siteName = get_setting('site_name', 'Y. Skembedjis & Sons Ltd');
$logoImg = get_image_path('logo', 'Skembedjis Logo');

// Determine current page to highlight active link
$current_page = basename($_SERVER['PHP_SELF']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($siteName); ?> - Leading Hardware Company in Cyprus</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Black+Ops+One&family=Open+Sans:ital,wght@0,300..800;1,300..800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="style.css">
</head>
<body>

    <!-- Header & Navigation -->
    <header class="main-header">
        <div class="container header-container">
            <a href="index.php" class="logo-link">
                <img src="<?php echo htmlspecialchars($logoImg); ?>" alt="<?php echo htmlspecialchars($siteName); ?> Logo">
            </a>
            
            <button class="mobile-toggle" id="menuToggle" aria-label="Toggle Navigation">☰</button>
            
            <ul class="nav-links" id="navLinks">
                <li class="nav-item dropdown">
                    <a href="products.php" class="nav-link <?php 
                        $product_pages = ['products.php', 'new-equipment.php', 'forklifts.php', 'vna.php', 'stackers.php', 'pallet-trucks.php', 'order-pickers.php', 'ramps.php', 'batteries-chargers.php', 'truck-mounted.php', 'handling.php', 'attachments.php', 'tyres.php', 'reach-trucks.php'];
                        echo in_array($current_page, $product_pages) ? 'active' : ''; 
                    ?>">Products <span class="dropdown-caret">▼</span></a>
                    <ul class="dropdown-menu">
                        <li><a href="forklifts.php">Forklifts</a></li>
                        <li><a href="catalog.php?category=cleaning">Cleaning Equipment</a></li>
                        <li><a href="catalog.php">Material Handling Equipment</a></li>
                        <li><a href="batteries-chargers.php">Batteries &amp; Chargers</a></li>
                        <li><a href="tyres.php">Tyres &amp; Wheels</a></li>
                        <li><a href="ramps.php">Ramps</a></li>
                        <li><a href="attachments.php">Attachments</a></li>
                        <li><a href="catalog.php?category=miscellaneous">Miscellaneous</a></li>
                    </ul>
                </li>
                <li class="nav-separator">|</li>
                <li class="nav-item">
                    <a href="spare-parts.php" class="nav-link <?php echo $current_page === 'spare-parts.php' ? 'active' : ''; ?>">Spare Parts</a>
                </li>
                <li class="nav-separator">|</li>
                <li class="nav-item dropdown">
                    <a href="services.php" class="nav-link <?php echo ($current_page === 'services.php' || $current_page === 'rentals.php' || $current_page === 'sell-machine.php' || $current_page === 'repairs-services.php' || $current_page === 'operator-training.php') ? 'active' : ''; ?>">Services <span class="dropdown-caret">▼</span></a>
                    <ul class="dropdown-menu">
                        <?php
                        $nav_services = get_services();
                        foreach ($nav_services as $ns):
                            $href = "services.php#" . $ns['slug'];
                            if ($ns['slug'] === 'rentals') {
                                $href = "rentals.php";
                            } elseif ($ns['slug'] === 'sell-machine') {
                                $href = "sell-machine.php";
                            } elseif ($ns['slug'] === 'repairs-services') {
                                $href = "repairs-services.php";
                            } elseif ($ns['slug'] === 'operator-training') {
                                $href = "operator-training.php";
                            }
                        ?>
                            <li><a href="<?php echo htmlspecialchars($href); ?>"><?php echo htmlspecialchars($ns['title']); ?></a></li>
                        <?php endforeach; ?>
                    </ul>
                </li>
                <li class="nav-separator">|</li>
                <li class="nav-item">
                    <a href="about.php" class="nav-link <?php echo $current_page === 'about.php' ? 'active' : ''; ?>">About</a>
                </li>
                <li class="nav-separator">|</li>
                <li class="nav-item">
                    <a href="blog.php" class="nav-link <?php echo $current_page === 'blog.php' ? 'active' : ''; ?>">Blog</a>
                </li>
                <li class="nav-item">
                    <button class="search-btn" aria-label="Search">🔍</button>
                </li>
            </ul>
        </div>
    </header>
