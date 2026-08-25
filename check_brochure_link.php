<?php
$html = file_get_contents('https://www.skembedjis.com/product/360-rotating-fork-positioners-with-forks/');
$pos = strpos($html, 'Download Brochure');
if ($pos !== false) {
    echo "Found at offset $pos:\n";
    echo substr($html, $pos - 150, 450) . "\n";
} else {
    echo "Not found\n";
}
