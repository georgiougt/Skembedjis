<?php
$db = new PDO('sqlite:db/site.db');
$stmt = $db->query("SELECT id, name, price, category_id FROM products WHERE category_id IN (11, 14)");
$prods = $stmt->fetchAll(PDO::FETCH_ASSOC);
foreach ($prods as $p) {
    echo "Cat {$p['category_id']} | ID {$p['id']} | Price: " . var_export($p['price'], true) . " | Name: {$p['name']}\n";
}
