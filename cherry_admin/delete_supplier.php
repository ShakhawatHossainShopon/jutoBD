<?php
include "../config/config.php";

$id = $_GET['id'] ?? null;
if (!$id) die("Invalid ID");

// Delete supplier
$stmt = $pdo->prepare("DELETE FROM suppliers WHERE id = ?");
$stmt->execute([$id]);

// Redirect back to list
header("Location: suppliers.php");
exit;
?>
