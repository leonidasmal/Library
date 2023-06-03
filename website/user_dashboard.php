<?php

include('connect.php');
session_start();


// Check if the user is logged in and the School_ID is set in the session
if (!isset($_SESSION['username']) || !isset($_SESSION['School_ID'])) {
  header("Location: front_page.php");
  exit;
}

$schoolID = $_SESSION['School_ID'];

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
  header("Location: front_page.php");
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
    <li><a href="user_dashboard.php">Home</a></li>
    <li><a href="user_view_books.php">Search Books</a></li>
      <li><a href="view_account.php">My Account</a></li> <!-- Updated link -->
      <li><a href="#">Library Events</a></li>
      <li><a href="contact_us.php">Contact Us</a></li>
      <li><a href="?logout">Log Out</a></li> <!-- Add logout link with query parameter -->    </ul>
  </nav>
</header>



<section class="main-content">
  <div class="container">
    <h2>Welcome <?php echo $_SESSION['username']; ?>!</h2>
    <p>Your School ID: <?php echo $schoolID; ?></p> <!-- Print the School_ID variable here -->
    <div class="dashboard-cards">
      <div class="card">
        <h3>Όλοι οι δανεισμοί</h3>
        <p>Δες όλα τα βιβλία που έχεις δανειστεί.</p>
        <a href="get_borrowed_books.php">Δες τα εδώ!</a>
      </div>
      <div class="card">
        <h3>Κάνε κράστηση</h3>
        <p>Ξέρεις ήδη ποιο βιβλίο θες?</p>
        <a href="make_reservation.php">Κλίκαρε εδώ!</a>
      </div>
    </div>
  </div>
</section>

<footer>
  <p>&copy; 2023 Library. All rights reserved.</p>
</footer>
</body>
</html>
