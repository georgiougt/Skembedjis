<?php
$db = new PDO('sqlite:db/site.db');
$stmt = $db->query("SELECT name, description, lifting_capacity, model FROM products WHERE category_id = 11 LIMIT 10");
$results = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($results as $r) {
    echo "Name: " . $r['name'] . "\n";
    echo "Model: " . $r['model'] . "\n";
    echo "Capacity: " . $r['lifting_capacity'] . "\n";
    echo "Description: " . substr($r['description'], 0, 150) . "...\n";
    echo "----------------------------------------\n";
}
