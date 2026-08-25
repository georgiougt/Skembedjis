<?php
$file = 'C:\Users\Georg\.gemini\antigravity-ide\brain\284ad6ac-0f30-43f7-9a01-865f8ca4d5ec\.system_generated\steps\1012\content.md';
if (!file_exists($file)) {
    echo "File not found.\n";
    exit;
}

$raw = file_get_contents($file);
preg_match_all('/\[([^\]]+)\]\((https:\/\/www\.skembedjis\.com\/product\/([^\/)]+)\/?)\)/is', $raw, $matches);

$uniqueSlugs = [];
if (!empty($matches[3])) {
    foreach ($matches[3] as $idx => $slug) {
        $slug = trim($slug);
        $text = trim($matches[1][$idx]);
        if (strpos($slug, 'product-category') !== false) continue;
        if (!isset($uniqueSlugs[$slug])) {
            $uniqueSlugs[$slug] = $text;
        }
    }
}

echo "Found " . count($uniqueSlugs) . " unique products:\n";
print_r($uniqueSlugs);
