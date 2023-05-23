<?php
include('connect.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
  header("Location: login.php");
  exit;
}

// Process the form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Retrieve the form data
  $currentPassword = $_POST['current_password'];
  $newPassword = $_POST['new_password'];
  $confirmPassword = $_POST['confirm_password'];

  // Retrieve the user's account information
  $username = $_SESSION['username'];
  $sql = "SELECT * FROM users WHERE username = '$username'";
  $result = mysqli_query($conn, $sql);

  if ($result && mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);

    // Verify the current password
    if (password_verify($currentPassword, $user['password'])) {
      // Check if the new password and confirm password match
      if ($newPassword === $confirmPassword) {
        // Hash the new password
        $hashedPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update the user's password in the database
        $updateSql = "UPDATE users SET password = '$hashedPassword' WHERE username = '$username'";
        mysqli_query($conn, $updateSql);

        // Redirect to the account page with a success message
        header("Location: view_account.php?success=1");
        exit;
      } else {
        // Passwords do not match
        $error = "New password and confirm password do not match.";
      }
    } else {
      // Current password is incorrect
      $error = "Current password is incorrect.";
    }
  } else {
    // User not found or error in retrieving data
    $error = "Error retrieving user account information.";
  }
}
?>



<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Change Password</title>
  <link rel="stylesheet" type="text/css" href="user_dash.css">
</head>
<body>
<header>
  <div class="logo">Library</div>
  <nav>
    <ul>
      <li><a href="user_dashboard.php">Home</a></li>
      <li><a href="search_book.php">Search Books</a></li>
      <li><a href="view_account.php">My Account</a></li>
      <li><a href="#">Library Events</a></li>
      <li><a href="#">Contact Us</a></li>
      <li><a href="login.php?logout=1">Log Out</a></li>
    </ul>
  </nav>
</header>

<section class="main-content">
  <div class="container">
    <h2>Change Password</h2>
    <?php if (isset($error)) { ?>
      <div class="error"><?php echo $error; ?></div>
    <?php } ?>
    <form method="POST" action="">
      <div>
        <label for="current_password">Current Password:</label>
        <input type="password" id="current_password" name="current_password" required>
      </div>
      <div>
        <label for="new_password">New Password:</label>
        <input type="password" id="new_password" name="new_password" required>
      </div>
      <div>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="confirm_password" name="confirm_password" required>
      </div>
      <div>
        <button type="submit">Change Password</button>
      </div>
    </form>
  </div>
</section>

<footer>
  <p>&copy; 2023 Library. All rights reserved.</p>
</footer>
</body>
</html>