<?php
// router.php for PHP built-in web server
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Strip the /new/ prefix if present to support the <base href="/new/"> tag
if (strpos($uri, '/new/') === 0) {
    $uri = '/' . substr($uri, 5);
}

// Check if URI is a physical file
$filepath = __DIR__ . $uri;
if ($uri !== '/' && file_exists($filepath) && !is_dir($filepath)) {
    $ext = strtolower(pathinfo($filepath, PATHINFO_EXTENSION));
    if ($ext === 'php') {
        include $filepath;
        exit;
    }
    $mimeTypes = [
        'css' => 'text/css',
        'js' => 'application/javascript',
        'png' => 'image/png',
        'jpg' => 'image/jpeg',
        'jpeg' => 'image/jpeg',
        'gif' => 'image/gif',
        'webp' => 'image/webp',
        'svg' => 'image/svg+xml',
        'ico' => 'image/x-icon',
        'pdf' => 'application/pdf',
        'woff' => 'font/woff',
        'woff2' => 'font/woff2',
        'ttf' => 'font/ttf',
    ];
    $mime = $mimeTypes[strtolower($ext)] ?? 'application/octet-stream';
    header("Content-Type: $mime");
    readfile($filepath);
    exit;
}

// Emulate rewrite rules
// 1. /products
if (preg_match('#^/products/?$#', $uri)) {
    include 'products.php';
    exit;
}

// 2. /product/slug
if (preg_match('#^/product/([^/]+)/?$#', $uri, $matches)) {
    $_GET['slug'] = $matches[1];
    include 'product-detail.php';
    exit;
}

// 3. /product-category/slug
if (preg_match('#^/(?:product-category/)?([^/]+)/?$#', $uri, $matches)) {
    $cat = $matches[1];
    // Legacy URL aliases — map friendly slugs to actual PHP file basenames
    $slug_aliases = [
        'tyres-2'              => 'tyres',
        'batteries'            => 'batteries-chargers',
        'about-us'             => 'about',
        'news'                 => 'blog',
        'parts'                => 'spare-parts',
        'spare-parts'          => 'spare-parts',
        'sell-your-machine'    => 'sell-machine',
        'sell-machine'         => 'sell-machine',
        'mobile-service-unit'  => 'repairs-services',
        'mobile-service-units' => 'repairs-services',
        'equipment'            => 'products',
        'our-equipment'        => 'products',
    ];
    if (isset($slug_aliases[$cat])) {
        $cat = $slug_aliases[$cat];
    }
    // List of known categories or pages
    $known_pages = ['about', 'careers', 'contact-us', 'operator-training', 'branches', 'parts', 'services', 'rentals', 'sell-machine', 'mobile-service-units', 'blog', 'new-equipment'];
    if (in_array($cat, $known_pages) || file_exists(__DIR__ . '/' . $cat . '.php')) {
        include $cat . '.php';
    } else {
        $_GET['category'] = $cat;
        include 'catalog.php';
    }
    exit;
}

// Fallback to index.php
include 'index.php';
