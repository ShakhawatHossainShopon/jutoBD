<?php
include "../config/config.php";

$id = $_GET['id'] ?? null;
if (!$id) die("Invalid ID");

// Fetch supplier
$supplier = $pdo->prepare("SELECT * FROM suppliers WHERE id = ?");
$supplier->execute([$id]);
$row = $supplier->fetch(PDO::FETCH_ASSOC);
if (!$row) die("Supplier not found");

// If form submitted, update
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = $_POST['name'];
    $des = $_POST['des'];
    $phone = $_POST['phone'];
    $website = $_POST['website'];

    $stmt = $pdo->prepare("UPDATE suppliers SET name=?, des=?, phone=?, website=? WHERE id=?");
    $stmt->execute([$name, $des, $phone, $website, $id]);

    header("Location: suppliers.php");
    exit;
}
ob_start();
?>

<div class="container">
  <div style="display:flex;justify-content:space-between; margin-bottom:2rem;">
    <h2>Add Suppliers</h2>
    <a class="button-primary" href="add_suppliers.php">Add Suppliers</a>
  </div>

  <form method="post">
      <input class="input" required type="text" name="name" value="<?= htmlspecialchars($row['name']) ?>"><br>
      <input class="input" type="text" name="phone" required value="<?= htmlspecialchars($row['phone']) ?>"><br>
      <input class="input" type="text" name="website" value="<?= htmlspecialchars($row['website']) ?>"><br><br>
  <textarea  class="textarea" type="text" placeholder="Des" name="des" value="<?= htmlspecialchars($row['des']) ?>"></textarea><br>
      <input class="button-primary" type="submit" value="Update">
  </form>
</div>
<?php
// Save buffered content
$content = ob_get_clean();
include 'layout.php';
?>
