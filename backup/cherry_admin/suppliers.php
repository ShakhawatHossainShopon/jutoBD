<?php
include "../config/config.php";
$title = "Home Page";

// Pagination setup
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch suppliers
$rows = $pdo->query("SELECT * FROM suppliers LIMIT $limit OFFSET $offset")->fetchAll(PDO::FETCH_ASSOC);

// Total rows & pages
$total = $pdo->query("SELECT COUNT(*) FROM suppliers")->fetchColumn();
$pages = ceil($total / $limit);
$range = 2; // pages before/after current

// Start output buffering
ob_start();
?>

<div class="container">
  <div style="display:flex;justify-content:space-between; margin-bottom:2rem;">
    <h2>Add Suppliers</h2>
    <a class="button-primary" href="add_suppliers.php">Add Suppliers</a>
  </div>

  <table>
    <tr>
      <th>No.</th>
      <th>id</th>
      <th>name</th>
      <th>des</th>
      <th>phone</th>
      <th>website</th>
      <th>action</th>
    </tr>
    <?php foreach ($rows as $i => $r): ?>
    <tr>
      <td><?= $offset + $i + 1 ?></td>
      <td><?= $r['id'] ?></td>
      <td><?= $r['name'] ?></td>
      <td><?= $r['des'] ?></td>
      <td><?= $r['phone'] ?></td>
      <td><?= $r['website'] ?></td>
      <td><a href="edit_supplier.php?id=<?= $r['id'] ?>">Edit</a> |  <a href="delete_supplier.php?id=<?= $r['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a></td>
    </tr>
    <?php endforeach; ?>
  </table>

  <!-- Pagination Links -->
  <div class="pagination">
      <?php if($page > 1): ?>
          <a href="?page=<?= $page-1 ?>" class="page-link">Prev</a>
      <?php endif; ?>

      <?php if($page > $range + 1): ?>
          <a href="?page=1" class="page-link">1</a>
          <?php if($page > $range + 2): ?>…<?php endif; ?>
      <?php endif; ?>

      <?php for($p = max(1, $page - $range); $p <= min($pages, $page + $range); $p++): ?>
          <a href="?page=<?= $p ?>" class="page-link <?= $p==$page?'active':'' ?>"><?= $p ?></a>
      <?php endfor; ?>

      <?php if($page < $pages - $range): ?>
          <?php if($page < $pages - $range - 1): ?>…<?php endif; ?>
          <a href="?page=<?= $pages ?>" class="page-link"><?= $pages ?></a>
      <?php endif; ?>

      <?php if($page < $pages): ?>
          <a href="?page=<?= $page+1 ?>" class="page-link">Next</a>
      <?php endif; ?>
  </div>

</div>

<?php
// Save buffered content
$content = ob_get_clean();
include 'layout.php';
?>
