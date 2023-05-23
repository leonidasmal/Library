<?php
include('connect.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}

// Retrieve the user's account information
$username = $_SESSION['username'];

// Modify the query based on your user table structure
$sql = "SELECT * FROM users WHERE username = '$username'";
$result = mysqli_query($conn, $sql);

if ($result && mysqli_num_rows($result) > 0) {
  $user = mysqli_fetch_assoc($result);
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Account</title>
  <link rel="stylesheet" type="text/css" href="user_dash.css">
  <style>
    .container {
      max-width: 800px;
      margin: 0 auto;
    }
    
    h2 {
      text-align: center;
      margin-top: 30px;
    }
    
    .account-details {
      display: flex;
      justify-content: space-between;
      margin-top: 20px;
    }
    
    .card {
      background-color: #f5f5f5;
      border-radius: 5px;
      padding: 20px;
      margin-bottom: 20px;
      box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
    }
    
    .card h3 {
      margin-top: 0;
      font-size: 18px;
    }
    
    .card p {
      margin-bottom: 20px;
    }
    
    .card a {
      display: inline-block;
      padding: 8px 16px;
      background-color: #4CAF50;
      color: white;
      text-decoration: none;
      border-radius: 5px;
    }
    
    .card a:hover {
      background-color: #45a049;
    }
  </style>
</head>
<body>
<header>
  <div class="logo">Library</div>
  <nav>
    <ul>
      <li><a href="user_dashboard.php">Home</a></li>
      <li><a href="search_book.php">Search Books</a></li>
      <li><a href="view_account.php">My Account</a></li> <!-- Updated link -->
      <li><a href="#">Library Events</a></li>
      <li><a href="#">Contact Us</a></li>
      <li><a href="?logout">Log Out</a></li> <!-- Add logout link with query parameter -->
    </ul>
  </nav>
</header>

<section class="main-content">
  <div class="container">
    <h2>Account Details</h2>
    <div class="account-details">
      <div class="card">
        <h3>Username: <?php echo $user['username']; ?></h3>
      </div>
      <div class="card">
        <h3>Αλλαγή Κωδικού</h3>
        <a href="change_password.php">Change Password</a>
      </div>
    </div>
  </div>
</section>

<footer>
  <p>&copy; 2023 Library. All rights reserved.</p>
</footer>
</body>
</html>

<?php
} else {
  // User not found
  echo "User account not found.";
}
mysqli_close($conn);
?>
