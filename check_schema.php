<?php
$db = new PDO('sqlite:db/site.db');
$stmt = $db->query('PRAGMA table_info(products)');
print_r($stmt->fetchAll(PDO::FETCH_ASSOC));
