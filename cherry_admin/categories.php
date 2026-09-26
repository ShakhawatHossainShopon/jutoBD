<?php
include "../config/config.php";
$title = "Home Page";

// Pagination setup
$limit = 5;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$offset = ($page - 1) * $limit;

// Fetch categories
$rows = $pdo->query("SELECT * FROM categories ORDER BY added_at DESC LIMIT $limit OFFSET $offset")->fetchAll(PDO::FETCH_ASSOC);

// Total rows & pages
$total = $pdo->query("SELECT COUNT(*) FROM categories")->fetchColumn();
$pages = ceil($total / $limit);
$range = 2; // pages before/after current

// Start output buffering
ob_start();
?>

<div class="container">
  <div style="display:flex;justify-content:space-between; margin-bottom:2rem;">
    <h2>Add Category</h2>
    <a class="button-primary" href="add_category.php">Add Category</a>
  </div>

  <table border="1" cellpadding="5" cellspacing="0">
    <tr>
      <th>No.</th>
      <th>id</th>
      <th>image</th>
      <th>title</th>
      <th>des</th>
      <th>action</th>
    </tr>
    <?php foreach ($rows as $i => $r): ?>
    <tr>
      <td><?= $offset + $i + 1 ?></td>
      <td><?= $r['id'] ?></td>
      <td>
        <?php if(!empty($r['image'])): ?>
          <img src="../<?= $r['image'] ?>" width="24" height="24">
        <?php endif; ?>
      </td>
      <td><?= $r['title'] ?></td>
      <td><?= $r['des'] ?></td>
      <td>
    <a href="edit_category.php?id=<?= $r['id'] ?>">Edit</a> |
    <a href="delete_category.php?id=<?= $r['id'] ?>" onclick="return confirm('Are you sure?')">Delete</a>
</td>

    </tr>
    <?php endforeach; ?>
  </table>

  <div class="pagination">
    <div>
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
</div>

<?php
$content = ob_get_clean();
include 'layout.php';
?>
