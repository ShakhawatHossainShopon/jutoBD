<?php
include "./config/config.php";

if($_SERVER['REQUEST_METHOD']==='POST'){
    $stmt = $pdo->prepare("INSERT INTO orders (product_id,name,phone,city,address,color,size,quantity,status,created_at) VALUES (?,?,?,?,?,?,?,?,?,NOW())");
    $res = $stmt->execute([
        $_POST['product_id'],
        $_POST['name'],
        $_POST['phone'],
        $_POST['city'],
        $_POST['address'],
        $_POST['color'],
        $_POST['size'],
        intval($_POST['quantity']),
        'pending'
    ]);
    echo json_encode(['success'=> $res]);
}
?>
