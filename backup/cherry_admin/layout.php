<?php
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit;
}
 ?>
<!DOCTYPE html>
<html>
<head>
    <title><?php echo $title ?? "My Site"; ?></title>
    <style>
    :root {
      --primary-bg-color: #65b741;
      --primary-bg-color-hover: #bbc863;
    }

    * {
      box-sizing: border-box;
      padding: 0;
      margin: 0;
      font-family: Arial, sans-serif; /* default font */
      line-height: 1.5; /* better readability */
    }

    body {
      background-color: #f8f8f8; /* optional background */
      color: #333; /* default text color */
    }

    img,
    video {
      max-width: 100%;
      height: auto;
      display: block;
    }

    a {
      text-decoration: none;
      color: inherit;
    }

    .navbar {
      background-color: #333;
      overflow: hidden;
    }

    /* Navbar links */
    .navbar a {
      float: left;
      display: block;
      color: white;
      text-align: center;
      padding: 14px 20px;
      text-decoration: none;
    }

    /* Hover effect */
    .navbar a:hover {
      text-decoration: underline;
      color: white;
    }
    .container {
      margin: 2rem 4%;
      background-color: white;
      padding: 26px;
      border-radius: 16px;
    }
    .button-primary {
      padding: 10px 16px;
      background-color: var(--primary-bg-color);
      cursor: pointer;
      z-index: 100;
      border-radius: 0.3rem;
      font-size: 14px;
      color: white;
      border: none;
      transition: linear 0.2s;
      text-transform: capitalize;
    }
    .button-primary:hover {
      background-color: var(--primary-bg-color-hover);
    }
    table {
        width: 100%;
        border-collapse: collapse;
        font-size: 12px;
        font-family: Arial, sans-serif;
        text-transform: capitalize;
    }

    th, td {
        border: 1px solid #ddd;
        padding: 10px;
        text-align: left;
    }

    th {
        background-color: #4CAF50;
        color: white;
    }

    tr:nth-child(even) {
        background-color: #f2f2f2;
    }

    tr:hover {
        background-color: #ddd;
    }

    a.button-primary {
        background-color: #4CAF50;
        color: white;
        padding: 8px 12px;
        text-decoration: none;
        border-radius: 5px;
    }
    .w-full {
      width: 100%;
    }
    .input {
      width: 100%; /* Full width */
      padding: 10px 15px; /* Space inside input */
      border: 1px solid #ccc; /* Border color */
      border-radius: 5px; /* Rounded corners */
      font-size: 16px; /* Text size */
      outline: none; /* Remove default focus outline */
      transition: 0.3s; /* Smooth transition for focus */
    }

    .input:focus {
      border-color: #007bff; /* Border color on focus */
      box-shadow: 0 0 5px rgba(0, 123, 255, 0.5); /* Light glow effect */
    }
    .py-2 {
      padding: 0 2rem;
    }
    .py-1 {
      padding: 0 1rem;
    }
    .my-2 {
      margin: 0 2rem;
    }
    .my-1 {
      margin: 0 1rem;
    }
  .input {
    width: 100%;
    padding: 6px 10px;
    margin: 8px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
    outline: none;
    transition: 0.3s;
    text-transform: capitalize;
  }

  .input:focus {
    border-color: green;
    box-shadow: 0 0 5px rgba(0,123,255,0.5);
  }
  .textarea{
    width: 100%;
    padding: 6px 10px;
    margin: 8px 0;
    border: 1px solid #ccc;
    border-radius: 5px;
    font-size: 14px;
    outline: none;
    transition: 0.3s;
    text-transform: capitalize;
  }
  .textarea:focus {
    border-color: green;
    box-shadow: 0 0 5px rgba(0,123,255,0.5);
  }
  h2{
    font-size: 22px;
  }
  .pagination {
      margin-top: 1rem;
      display: flex;
      gap: 5px;
      flex-wrap: wrap;
      font-size: 12px;
      justify-content: end;
  }
  .page-link {
      padding: 5px 10px;
      text-decoration: none;
      border: 1px solid #4CAF50;
      color: #4CAF50;
      border-radius: 4px;
      transition: 0.2s;
  }
  .page-link:hover { background-color: #4CAF50; color: white; }
  .page-link.active { background-color: #4CAF50; color: white; font-weight: bold; }

</style>
</head>
<body>
    <header>
    <div class="navbar">
    <a href="index.php">Home</a>
    <a href="products.php">Products</a>
    <a href="order_board.php">Orders Board</a>
    <a href="categories.php">Categories</a>
    <a href="suppliers.php">Suplliers</a>
    <a href="all_orders.php">All Orders</a>
    <a href="add_order.php">Create Orders</a>
    <a href="accounts.php">Reports</a>
    <a href="admins.php">Admin</a>
    <a href="admin_banners.php">Banner</a>
    <a href="logout.php">Logout</a>
  </div>
    </header>

    <main>
        <?php echo $content; // page content goes here ?>
    </main>

</body>
</html>
