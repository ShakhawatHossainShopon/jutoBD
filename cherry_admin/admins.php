<?php
include "../config/config.php";
if (!isset($_SESSION['admin_id'])) header("Location: login.php");

// Add Admin
if (isset($_POST['add'])) {
    $username = $_POST['username'];
    $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
    $stmt = $pdo->prepare("INSERT INTO admins (username, password) VALUES (?, ?)");
    $stmt->execute([$username, $password]);
}

// Delete Admin
if (isset($_GET['delete'])) {
    $stmt = $pdo->prepare("DELETE FROM admins WHERE id=?");
    $stmt->execute([$_GET['delete']]);
}

// Fetch all admins
$admins = $pdo->query("SELECT * FROM admins")->fetchAll(PDO::FETCH_ASSOC);

ob_start();
?>
<div class="container">
<h2>Admins</h2>
<br>

<form method="post">
    <input type="text" class="input" name="username" placeholder="Username" required>
    <input class="input" type="password" name="password" placeholder="Password" required>
    <button type="submit" name="add" class="button-primary" >Add Admin</button>
</form>
<br>
<table >
    <tr><th>ID</th><th>Username</th><th>Action</th></tr>
    <?php foreach($admins as $a): ?>
    <tr>
        <td><?= $a['id'] ?></td>
        <td><?= htmlspecialchars($a['username']) ?></td>
        <td>
            <a href="?delete=<?= $a['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
        </td>
    </tr>
    <?php endforeach; ?>
</table>
</div>




<?php
$content = ob_get_clean();
include 'layout.php';
?>
