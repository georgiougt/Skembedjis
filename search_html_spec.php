<?php
$context = stream_context_create([
    'http' => [
        'header' => "User-Agent: Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/120.0.0.0 Safari/537.36\r\n"
    ]
]);
$html = file_get_contents('https://www.skembedjis.com/product/360-rotating-fork-positioners-with-forks/', false, $context);
$pos = strpos($html, 'KB15');
if ($pos !== false) {
    echo "Found 'KB15' at offset $pos:\n";
    echo substr($html, $pos - 150, 450) . "\n";
} else {
    echo "'KB15' not found in raw HTML.\n";
}
