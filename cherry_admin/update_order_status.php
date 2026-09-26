<?php
include "../config/config.php";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $order_id = $_POST['order_id'] ?? null;
    $status = $_POST['status'] ?? null;

    // Validate inputs
    $valid_statuses = ['pending','processing','completed','cancelled'];
    if (!$order_id || !$status || !in_array($status, $valid_statuses)) {
        die("Invalid input");
    }

    // Update order status
    $stmt = $pdo->prepare("UPDATE orders SET status = :status WHERE id = :id");
    $stmt->execute([
        ':status' => $status,
        ':id' => $order_id
    ]);

    // Redirect back to the order board
    header("Location: order_board.php");
    exit;
}
?>
