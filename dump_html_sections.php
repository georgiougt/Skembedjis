<?php
$html = file_get_contents('sample_product.html');
// Let's dump all h1 tags
preg_match_all('/<h1[^>]*>(.*?)<\/h1>/is', $html, $h1s);
echo "H1s:\n";
print_r($h1s[1]);

// Let's search for "woocommerce-product-gallery__image" and print surrounding lines
$lines = file('sample_product.html');
$found = 0;
foreach ($lines as $i => $l) {
    if (strpos($l, 'woocommerce-product-gallery') !== false || strpos($l, 'product_title') !== false || strpos($l, 'Price') !== false) {
        echo ($i+1) . ': ' . trim($l) . "\n";
        $found++;
        if ($found > 30) break;
    }
}
