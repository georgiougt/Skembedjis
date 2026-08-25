<?php
$html = file_get_contents('sample_product.html');
$needle = 'ELEVATING PLATFORM SAFETY LANYARD HARNESS';
$offset = 0;
while (($pos = strpos($html, $needle, $offset)) !== false) {
    echo "Found at offset $pos:\n";
    echo substr($html, $pos - 100, 200) . "\n\n";
    $offset = $pos + strlen($needle);
}
