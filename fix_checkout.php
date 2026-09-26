<?php
$f = "C:/xampp/htdocs/checkout.php";
$c = file_get_contents($f);

// 1. First query (POST processing block)
$c = str_replace(
    'SELECT id, name, price, wholesale, colors, size FROM products WHERE id IN ($placeholders)', 
    'SELECT p.id, p.name, p.price, p.wholesale, p.colors, p.size, d.discount_percent, d.end_date FROM products p LEFT JOIN discounts d ON p.id = d.product_id AND d.end_date > NOW() WHERE p.id IN ($placeholders)'
, $c);

// 2. Adjusting the locked_price to use discount logic
$c = str_replace(
    '$locked_price = $p[\'price\'] ?: 0;',
    '$locked_price = $p[\'price\'] ?: 0;
            if (!empty($p[\'discount_percent\'])) {
                $locked_price = $locked_price - ($locked_price * ($p[\'discount_percent\'] / 100));
            }',
    $c
);

// 3. Second query (UI rendering block)
$c = str_replace(
    'SELECT id, name, price, images FROM products WHERE id IN ($placeholders)',
    'SELECT p.id, p.name, p.price, p.images, d.discount_percent, d.end_date FROM products p LEFT JOIN discounts d ON p.id = d.product_id AND d.end_date > NOW() WHERE p.id IN ($placeholders)',
    $c
);

// 4. Adjusting the UI pricing logic
$c = str_replace(
    '$subtotal += ($p["price"] * $qty);',
    '$active_price = $p["price"];
        if (!empty($p["discount_percent"])) {
            $active_price = $active_price - ($active_price * ($p["discount_percent"] / 100));
        }
        $subtotal += ($active_price * $qty);',
    $c
);

$c = str_replace(
    '"price" => $p["price"],',
    '"price" => $active_price,',
    $c
);

file_put_contents($f, $c);
