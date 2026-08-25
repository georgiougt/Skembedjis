<?php
$db = new PDO('sqlite:db/site.db');
$db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

$slugs = [
    'cleanserv-sd43-180',
    'cleanserv-vd6',
    'scrubmaster-b120r',
    'scrubmaster-b25',
    'scrubmaster-b70',
    'sweepmaster-650',
    'sweepmaster-900r',
    'sweepmaster-b500',
    'sweepmaster-m600'
];

$context = stream_context_create([
    'http' => [
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64)\r\n"
    ]
]);

$stmtUpdate = $db->prepare("UPDATE products SET description = :description, gallery_images = NULL WHERE slug = :slug");

foreach ($slugs as $slug) {
    $url = "https://www.skembedjis.com/product/{$slug}/";
    echo "Scraping details table for $slug ($url) ...\n";
    $html = @file_get_contents($url, false, $context);
    if (!$html) {
        echo "  FAILED to fetch $url\n";
        continue;
    }

    $fullContent = '';

    // Extract short description / paragraph
    if (preg_match('#<div[^>]*class="[^"]*woocommerce-product-details__short-description[^"]*"[^>]*>(.*?)</div>#is', $html, $m)) {
        $fullContent .= '<div class="product-info-summary" style="margin-bottom: 2rem; color: #475569; font-size: 1rem; line-height: 1.7;">' . trim($m[1]) . '</div>';
    }

    // Extract table(s) or description tab content
    if (preg_match('#<table[^>]*>.*?</table>#is', $html, $m)) {
        $fullContent .= '<div class="product-specs-table-wrap" style="overflow-x: auto; margin-top: 1.5rem;">' . trim($m[0]) . '</div>';
    } elseif (preg_match('#<div[^>]*id="tab-description"[^>]*>(.*?)</div>#is', $html, $m)) {
        $fullContent .= '<div class="product-info-details" style="margin-top: 1.5rem; color: #475569; font-size: 0.95rem; line-height: 1.6;">' . trim($m[1]) . '</div>';
    }

    if ($fullContent) {
        $stmtUpdate->execute([
            ':description' => $fullContent,
            ':slug' => $slug
        ]);
        echo "  Successfully updated $slug description and table!\n";
    } else {
        echo "  No detailed table/description found for $slug\n";
    }
}

echo "Finished updating all cleaning products!\n";
