<?php
$context = stream_context_create([
    'http' => [
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n"
    ]
]);
$html = file_get_contents('https://www.skembedjis.com/product/360-rotating-fork-positioners-with-forks/', false, $context);
$pos = strpos($html, 'shop_attributes');
if ($pos !== false) {
    echo "Found 'shop_attributes' at offset $pos:\n";
    echo substr($html, $pos - 150, 600) . "\n";
} else {
    echo "'shop_attributes' not found in raw HTML.\n";
}
// Let's print any table contents
preg_match_all('/<table[^>]+class="[^"]*shop_attributes[^"]*"[^>]*>(.*?)<\/table>/is', $html, $matches);
print_r($matches);
