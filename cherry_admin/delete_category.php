<?php
include "../config/config.php";

$id = $_GET['id'] ?? null;
if (!$id) die("Invalid ID");

// Optionally delete the image file
$stmt = $pdo->prepare("SELECT image FROM categories WHERE id=?");
$stmt->execute([$id]);
$row = $stmt->fetch(PDO::FETCH_ASSOC);
if ($row && !empty($row['image']) && file_exists("../".$row['image'])) {
    unlink("../".$row['image']);
}

// Delete category
$stmt = $pdo->prepare("DELETE FROM categories WHERE id=?");
$stmt->execute([$id]);

header("Location: categories.php");
exit;
?>
