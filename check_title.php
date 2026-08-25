<?php
$html = file_get_contents('sample_product.html');
preg_match('/<title>(.*?)<\/title>/is', $html, $matches);
print_r($matches);
