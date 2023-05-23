<?php
session_start();
include('connect.php');

if (isset($_POST['submit'])) {
  $opwd = $_POST['opwd'];
  $npwd = $_POST['npwd'];
  $cpwd = $_POST['cpwd'];
  $username = $_SESSION['username'];

  $sql = "SELECT user_password FROM users WHERE username = '$username'";
  $result = mysqli_query($conn, $sql);
  $num = mysqli_fetch_array($result);

  if ($result) {
    $storedPassword = $num['user_password'];

    if ($opwd === $storedPassword) {
      if ($npwd === $cpwd) {
        $updateSql = "UPDATE users SET user_password ='$npwd' WHERE username = '$username'";
        $updateResult = mysqli_query($conn, $updateSql);

        if ($updateResult) {
          $_SESSION['msg1'] = "Password Changed Successfully";
        } else {
          $_SESSION['msg2'] = "Error updating password: " . mysqli_error($conn);
        }
      } else {
        $_SESSION['msg2'] = "New password and confirm password do not match";
      }
    } else {
      $_SESSION['msg2'] = "Current password is incorrect";
    }
  } else {
    $_SESSION['msg2'] = "Error retrieving user account information: " . mysqli_error($conn);
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
        <label for="current_password">Current Password:</label>
        <input type="password" id="opwd" name="opwd" required>
      </div>
      <div>
        <label for="new_password">New Password:</label>
        <input type="password" id="npwd" name="npwd" required>
      </div>
      <div>
        <label for="confirm_password">Confirm Password:</label>
        <input type="password" id="cpwd" name="cpwd" required>
      </div>
      <div>
        <button type="submit" name="submit">Change Password</button>
      </div>
    </form>
  </div>
</section>

<footer>
  <p>&copy; 2023 Library. All rights reserved.</p>
</footer>
</body>
</html>
