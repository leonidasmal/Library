<?php
include('connect.php');
session_start();
// Check if the user is logged in and the School_ID is set in the session
if (!isset($_SESSION['username']) || !isset($_SESSION['School_ID'])) {
  header("Location: front_page.php");
  exit;
}

$managerID = $_SESSION['Manager_ID'];
$managerUsername = $_SESSION['username'];

// Logout functionality
if (isset($_GET['logout'])) {
  session_destroy(); // Destroy all session data
  header("Location: front_page.php");
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Manager_dashboard</title>
  <link rel="stylesheet" type="text/css" href="user_dash.css">
</head>
<body>
<header>
  <div class="logo">Library</div>
  <nav>
    <ul>
    <li><a href="operator_manager_dashboard.php">Home</a></li>
    <li><a href="view_account.php">My Account</a></li> 
      <li><a href="front_page.php">Log out</a></li>  </ul>
      </nav>
</header>



<section class="main-content">
  <div class="container">
    <h2>Welcome <?php echo $_SESSION['username']; ?>!</h2>
    <div class="dashboard-cards">

    <div class="card">
  <h3>Overview Books</h3>
  <a href="school_details.php">View books</a>
</div>
      <div class="card">
        <h3>Overview Loans</h3>
        <a href="manage_loans.php">Manage loan books</a>
      </div>

      <div class="card">
        <h3>Late Loans</h3>
        <a href="late_loans.php">View late loans</a>
      </div>

      <div class="card">
        <h3>Average Rating Score per borrower and category </h3>
        <a href="average_ratings.php">View averages</a>
      </div>




      <div class="card">
  <h3>Overview Reservations</h3>
  <a href="manage_reservations.php">Manage reservations on books</a>
</div>
<div class="card">
  <h3>Overview Users</h3>
  <a href="manage_users.php">Manage users</a>
</div>
<div class="card">
  <h3>Overview Registrations</h3>
  <a href="manager.php">Approve/Deny Users</a>
</div>

<div class="card">
  <h3>Manage Reviews</h3>
  <a href="manage_reviews.php">See student reviews</a>
</div>
  </div>



  
</section>

<footer>
  <p>&copy; 2023 Library. All rights reserved.</p>
</footer>
</body>
</html>
