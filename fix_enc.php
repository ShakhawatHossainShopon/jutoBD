<?php
$f = "C:/xampp/htdocs/api/cart.php";
$c = file_get_contents($f);
$c = str_replace("৳", "?", $c);
file_put_contents($f, $c);
