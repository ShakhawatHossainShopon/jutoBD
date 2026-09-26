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
    if ($stmt->execute([':status' => $status, ':id' => $order_id])) {
        
        // Fetch order details to send the email
        $sql = "SELECT * FROM orders WHERE id = ?";
        $stmt2 = $pdo->prepare($sql);
        $stmt2->execute([$order_id]);
        $order = $stmt2->fetch(PDO::FETCH_ASSOC);

        // Fetch product names for email
        $itemStmt = $pdo->prepare("SELECT p.name FROM order_items oi JOIN products p ON oi.product_id = p.id WHERE oi.order_id = ?");
        $itemStmt->execute([$order_id]);
        $items = $itemStmt->fetchAll(PDO::FETCH_ASSOC);
        $itemCount = count($items);
        $product_name = $itemCount > 1 ? $itemCount . " Items" : ($items[0]['name'] ?? 'Unknown Product');
        $order['product_name'] = $product_name;

        // If customer provided an email, send them a beautiful notification
        if ($order && !empty($order['email'])) {
            $to = $order['email'];
            $subject = "Your Order is now " . ucfirst($status) . " - #" . $order['id'];
            
            // Build a gorgeous HTML email
            $html = "
            <html>
            <body style='font-family: -apple-system, BlinkMacSystemFont, \"Segoe UI\", Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; padding: 40px 20px; margin: 0;'>
                <div style='background-color: #ffffff; border-radius: 24px; padding: 40px; box-shadow: 0 10px 40px -10px rgba(0, 0, 0, 0.1); text-align: center; max-width: 500px; margin: 0 auto; border: 1px solid #f1f5f9;'>
            ";

            if ($status === 'processing') {
                $html .= "
                    <div style='background-color: #eff6ff; color: #3b82f6; width: 64px; height: 64px; border-radius: 20px; line-height: 64px; font-size: 32px; margin: 0 auto 24px auto;'>📦</div>
                    <h1 style='color: #0f172a; margin-bottom: 12px; font-size: 24px; font-weight: 800; letter-spacing: -0.5px;'>Great news, " . htmlspecialchars($order['name']) . "!</h1>
                    <p style='color: #64748b; font-size: 15px; line-height: 24px; margin-bottom: 32px;'>Your order for <strong>" . htmlspecialchars($order['product_name']) . "</strong> is now being processed. Our team is packing it up and getting it ready for delivery.</p>";
            } elseif ($status === 'completed') {
                $html .= "
                    <div style='background-color: #ecfdf5; color: #10b981; width: 64px; height: 64px; border-radius: 20px; line-height: 64px; font-size: 32px; margin: 0 auto 24px auto;'>🎉</div>
                    <h1 style='color: #0f172a; margin-bottom: 12px; font-size: 24px; font-weight: 800; letter-spacing: -0.5px;'>Order Completed!</h1>
                    <p style='color: #64748b; font-size: 15px; line-height: 24px; margin-bottom: 32px;'>Hi " . htmlspecialchars($order['name']) . ", your order for <strong>" . htmlspecialchars($order['product_name']) . "</strong> has been successfully fulfilled. Thank you so much for shopping with us!</p>";
            } else {
                $html .= "
                    <div style='background-color: #f1f5f9; color: #64748b; width: 64px; height: 64px; border-radius: 20px; line-height: 64px; font-size: 32px; margin: 0 auto 24px auto;'>📋</div>
                    <h1 style='color: #0f172a; margin-bottom: 12px; font-size: 24px; font-weight: 800; letter-spacing: -0.5px;'>Order Update</h1>
                    <p style='color: #64748b; font-size: 15px; line-height: 24px; margin-bottom: 32px;'>Hi " . htmlspecialchars($order['name']) . ", your order status has been updated to <strong>" . ucfirst($status) . "</strong>.</p>";
            }

            $html .= "
                    <div style='background-color: #f8fafc; border-radius: 16px; padding: 24px; text-align: left; border: 1px solid #f1f5f9;'>
                        <p style='margin: 0 0 12px 0; color: #334155; font-size: 14px;'><strong style='color: #94a3b8; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 4px;'>Order ID</strong> #" . $order['id'] . "</p>
                        <p style='margin: 0 0 12px 0; color: #334155; font-size: 14px;'><strong style='color: #94a3b8; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 4px;'>Delivery Address</strong> " . htmlspecialchars($order['address']) . ", " . htmlspecialchars($order['city']) . "</p>
                        <p style='margin: 0 0 0 0; color: #334155; font-size: 14px;'><strong style='color: #94a3b8; font-size: 12px; text-transform: uppercase; letter-spacing: 1px; display: block; margin-bottom: 4px;'>Payment Method</strong> " . htmlspecialchars($order['payment_method']) . "</p>
                    </div>
                </div>
            </body>
            </html>
            ";

            // Headers for HTML email
            $headers = "MIME-Version: 1.0" . "\r\n";
            $headers .= "Content-type:text/html;charset=UTF-8" . "\r\n";
            $headers .= "From: updates@yourstore.com" . "\r\n";

            // Send the email (Requires MTA/SMTP like Sendmail installed on server)
            @mail($to, $subject, $html, $headers);
        }

        $_SESSION['msg'] = "Order status updated to " . ucfirst($status) . "!";
        $_SESSION['msg_type'] = "success";
    } else {
        $_SESSION['msg'] = "Failed to update order status.";
        $_SESSION['msg_type'] = "error";
    }

    // Redirect back to the order board
    header("Location: order_board");
    exit;
}
?>
