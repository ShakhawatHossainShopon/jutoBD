<?php
include "../config/config.php";

$id = $_GET['id'] ?? null;
if (!$id) die("Invalid Product ID");

// 1. Get product images first
$stmt = $pdo->prepare("SELECT images FROM products WHERE id=?");
$stmt->execute([$id]);
$product = $stmt->fetch(PDO::FETCH_ASSOC);

if ($product) {
    $images = json_decode($product['images'], true); // Assuming images stored as JSON
    if ($images) {
        foreach ($images as $img) {
            $file_path = "../" . $img; // adjust path if needed
            if (file_exists($file_path)) {
                unlink($file_path); // delete file
            }
        }
    }

    // 2. Delete product from DB
    $stmt = $pdo->prepare("DELETE FROM products WHERE id=?");
    $stmt->execute([$id]);
}

header("Location: products.php");
exit;
