<?php
include "../config/config.php";

if ($_SERVER['REQUEST_METHOD']=='POST') {
  $name =  $_POST['name'];
  $phone =  $_POST['phone'];
  $website =  $_POST['website'];
  $des =  $_POST['des'];

  $stmt = $pdo->prepare('INSERT INTO suppliers (name,phone,website,des) VALUES (?,?,?,?)');
  if ($stmt->execute([$name, $phone, $website, $des])) {
      $_SESSION['msg'] = "Supplier Added";
  } else {
      $_SESSION['msg'] = "Error occurred!";
  }
  header('Location: suppliers.php');
  exit();
}
// Start output buffering
ob_start();
?>
<div class="container">
  <div  style="display:flex;justify-content:start; margin-bottom: 1rem;">
    <h2>Add Suppliers</h2>
  </div>

  <div>
    <form enctype="multipart/form-data" action="add_suppliers.php" method="post">
      <div class="w-full">
        <input class="input" type="text" name="name" required value="" placeholder="john doe">
        <input class="input" type="text" name="phone" required value="" placeholder="01642956206">
        <input class="input" type="text" name="website" value="" placeholder="www.example.com">
        <textarea class="textarea" name="des" cols="60" placeholder="description"></textarea>
      </div>
      <button type="submit" class="button-primary" style="margin-top:1rem" name="button">Add Suppliers</button>
        </form>
      </div>
    </div>
    <?php
// Save buffered content
$content = ob_get_clean();
// Include the master layout
include 'layout.php';
?>
