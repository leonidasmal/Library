<?php
include('connect.php');
session_start();
// Check if the user is logged in
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}

// Logout functionality
if (isset($_GET['logout'])) {
  session_destroy(); // Destroy all session data
  header("Location: login.php");
  exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Dashboard</title>
  <link rel="stylesheet" type="text/css" href="user_dash.css">
</head>
<body>
<header>
  <div class="logo">Library</div>
  <nav>
    <ul>
    <li><a href="edit_books.php">Home</a></li>
      <li><a href="#">Library Events</a></li>
      <li><a href="contact_us.php">Contact Us</a></li>
      <li><a href="?logout">Log Out</a></li> <!-- Add logout link with query parameter -->    </ul>
  </nav>
</header>



<section class="main-content">
  <div class="container">
    <h2>Welcome <?php echo $_SESSION['username']; ?>!</h2>
    <div class="dashboard-cards">

    <div class="card">
  <h3>Overview Books</h3>
  <a href="school_details.php?School_ID=<?php echo $_SESSION['School_ID']; ?>">View books</a>
</div>
      <div class="card">
        <h3>Overview Loans</h3>
        <a href="manage_reservations.php">Manage loan books</a>
      </div>
      <div class="card">
  <h3>Overview Reservations</h3>
  <a href="manage_reservations.php">Manage reservations on books</a>
</div>
<div class="card">
  <h3>Overview Registrations</h3>
  <a href="manager.php">Approve/Deny Users</a>
</div>
<div class="card">
  <h3>Manage Reviews</h3>
  <a href="manager.php">See student reviews</a>
</div>
  </div>
</section>

<footer>
  <p>&copy; 2023 Library. All rights reserved.</p>
</footer>
</body>
</html>
