<?php
session_start();
include("connect.php");

$adminID = $_SESSION['Admin_ID'];
// Check if the admin is logged in
if (!isset($_SESSION['admin'])) {
  header("Location: admin_login.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin_dashboard</title>
  <link rel="stylesheet" type="text/css" href="admin_dashboard.css">
</head>
<body>
<header>
  <div class="logo">Library</div>
  <nav>
    <ul>
    <li><a href="admin_dashboard.php">Home</a></li>
    <li><a href="view_account.php">My Account</a></li>
      <li><a href="front_page.php">Log Out</a></li></ul>
  </nav>
</header>



<section class="main-content">
  <div class="container">
    <h2>Welcome <?php echo $_SESSION['username']; ?>!</h2>
    <div class="dashboard-cards">

    <div class="card">
  <h3>Overview School Units</h3>
  <a href="register_schools.php">Register New Schools,Update them or Delete them</a>
</div>

<div class="card">
  <h3>Overview Registrations</h3>
  <a href="admin.php">Approve/Deny Operators </a>
</div>
<div class="card">
  <h3>Find total number of loans per school for a specific year and month</h3>
  <a href="3.1.1.php">Loans per school </a>
</div>
<div class="card">
<h3>Retrieve authors belonging to a specific category and identify teachers who have borrowed books from this category in the past year</h3>
  <a href="3.1.2.php">Select the Category</a>
</div>
<div class="card">
  <h3>Young professors who have borrowed the most.</h3>
  <a href="3.1.3.php">See the table</a>
</div>
<div class="card">
  <h3>Authors whose books have not been borrowed.</h3>
  <a href="3.1.4.php">See the table</a>
</div>
<div class="card">
  <h3>Managers that have the same number of loans in a year, with more than 20 of them.</h3>
  <a href="3.1.5.php">See the table</a>
</div>
<div class="card">
  <h3>Top three pairs of category fields, in loaned books.</h3>
  <a href="3.1.6.php">See the table</a>
</div>
<div class="card">
  <h3> All authors who have written at least 5 books less than the author with the most books.</h3>
  <a href="3.1.7.php">See the table</a>
</div>
<div class="card">
  <h3>Backup Database</h3>
  <a href="backup.php">Perform Database Backup</a>
</div>
<div class="card">
  <h3>Reset Database</h3>
  <a href="reset_DB.php">Execute Database Reset</a>
</div>
<div class="card">
  <h3>Manage Operators</h3>
  <a href="manage_operators.php">Execute Database Reset</a>
</div>

  </div>
</section>
</body>
<footer>
  <p>&copy; 2023 Library. All rights reserved.</p>
</footer>
</html>
