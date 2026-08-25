<?php
$raw = file_get_contents('items.json');
$data = json_decode($raw, true);
echo "JSON Keys: " . implode(', ', array_keys($data)) . "\n";
if (isset($data['return'])) {
    echo "return keys: " . implode(', ', array_keys($data['return'])) . "\n";
    if (is_array($data['return'])) {
        echo "return is array, count: " . count($data['return']) . "\n";
        // print first item
        print_r(array_slice($data['return'], 0, 1));
    }
}
