<?php
session_start();
include('connect.php');

if (isset($_POST['submit'])) {
  $ouser = $_POST['ouser'];
  $nuser = $_POST['nuser'];
  $password = $_POST['password'];
  $username = $_SESSION['username'];

  $sql = "SELECT username, user_password FROM users WHERE username = '$username'";
  $result = mysqli_query($conn, $sql);
  $num = mysqli_fetch_array($result);

  $checkSql = "SELECT username FROM users WHERE username = '$nuser'";
  $checkResult = mysqli_query($conn, $checkSql);

  if (mysqli_num_rows($checkResult) > 0) {
    $_SESSION['msg2'] = "Username already exists. Please choose a different username.";
    header("Location: change_username.php");
    exit;
  }

  if ($result) {
    if ($num['username'] === $username && $num['user_password'] === $password) {
      $updateSql = "UPDATE users SET username ='$nuser' WHERE username = '$username'";
      $updateResult = mysqli_query($conn, $updateSql);

      if ($updateResult) {
        $_SESSION['msg1'] = "Username has been successfully changed. Please log out and log in again from the front page in order to access your account with the new username.";
    } else {
        $_SESSION['msg2'] = "Error updating username: " . mysqli_error($conn);
      }
    } else {
      $_SESSION['msg2'] = "Incorrect password";
    }
  } else {
    $_SESSION['msg2'] = "Error retrieving user account information: " . mysqli_error($conn);
  }
}

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
  <title>Change Username</title>
  <link rel="stylesheet" type="text/css" href="user_dash.css">
  <style>
    body {
      font-family: Arial, sans-serif;
    }

    .container {
      width: 400px;
      margin: 0 auto;
    }

    h2 {
      text-align: center;
      margin-top: 30px;
    }

    form {
      margin-top: 30px;
    }

    label {
      display: block;
      margin-bottom: 10px;
    }

    input[type="password"] {
      width: 100%;
      padding: 8px;
      margin-bottom: 20px;
    }

    button[type="submit"] {
      padding: 10px 20px;
      background-color: #333;
      color: #fff;
      border: none;
      cursor: pointer;
    }

    button[type="submit"]:hover {
      background-color: #000;
    }

    .error-message {
      color: red;
    }

    .success-message {
      color: green;
      font-size: 18px;
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
    <h2>Change Username</h2>
    <?php if (isset($_SESSION['msg1'])) { ?>
      <p class="success-message" style="font-size: 20px;"><?php echo $_SESSION['msg1']; ?></p>
      <?php unset($_SESSION['msg1']); ?>
    <?php } ?>
    <?php if (isset($_SESSION['msg2'])) { ?>
      <p class="error-message"><?php echo $_SESSION['msg2']; ?></p>
      <?php unset($_SESSION['msg2']); ?>
    <?php } ?>
    <form method="POST" action="">
      <div>
        <label for="current_username">Current username:</label>
        <input type="username" id="ouser" name="ouser" required>
      </div>
      <div>
        <label for="new_username">New username:</label>
        <input type="username" id="nuser" name="nuser" required>
      </div>
      <div>
    <label for="password">Password:</label>
    <input type="password" id="password" name="password" required>
  </div>
    
        <button type="submit" name="submit">Change username</button>
      </div>
    </form>
  </div>
</section>

<footer>
  <p>&copy; 2023 Library. All rights reserved.</p>
</footer>
</body>
</html>
