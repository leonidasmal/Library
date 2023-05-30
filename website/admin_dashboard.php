<?php
session_start();
include("connect.php");

$adminID = $_SESSION['Admin_ID'];
var_dump($adminID);
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
  <link rel="stylesheet" type="text/css" href="user_dash.css">
</head>
<body>
<header>
  <div class="logo">Library</div>
  <nav>
    <ul>
    <li><a href="admin_dashboard.php">Home</a></li>
      <li><a href="#">Library Events</a></li>
     
      <li><a href="front_page.php">Log out</a></li> <!-- Add logout link with query parameter -->    </ul>
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
  <a href="admin.php?Admin_ID=<?php echo $_SESSION['Admin_ID']; ?>">Approve/Deny Users</a>
</div>

  </div>
</section>

<footer>
  <p>&copy; 2023 Library. All rights reserved.</p>
</footer>
</body>
</html>
