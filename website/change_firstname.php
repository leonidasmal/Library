<?php
session_start();
include('connect.php');

if (isset($_POST['submit'])) {
  $currentFirstName = $_POST['current_first_name'];
  $newFirstName = $_POST['new_first_name'];
  $password = $_POST['password'];
  $username = $_SESSION['username'];

  $sql = "SELECT first_name, user_password FROM users WHERE username = '$username'";
  $result = mysqli_query($conn, $sql);
  $row = mysqli_fetch_assoc($result);

  if ($result) {
    if ($row['first_name'] === $currentFirstName && $row['user_password'] === $password) {
      $updateSql = "UPDATE users SET first_name ='$newFirstName' WHERE username = '$username'";
      $updateResult = mysqli_query($conn, $updateSql);

      if ($updateResult) {
        $_SESSION['msg1'] = "Your first name has been successfully changed. Please log out and log in again from the front page in order to access your account with the new first name.";
       
      } else {
        $_SESSION['msg2'] = "Error updating first name: " . mysqli_error($conn);
      }
    } else {
      $_SESSION['msg2'] = "Incorrect password or current first name";
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
  <title>Change First Name</title>
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
    <?php if (isset($_SESSION['Admin_ID'])) { ?>
      <li><a href="admin_dashboard.php">Home</a></li>
      <?php } elseif(isset($_SESSION['Manager_ID'])) { ?>
        <li><a href="operator_manager_dashboard.php">Home</a></li>
        <?php } else { ?>
          <li><a href="user_dashboard.php">Home</a></li>
          <?php } ?>


      <li><a href="view_account.php">My Account</a></li>

      <li><a href="front_page.php">Log Out</a></li>  
    </ul>
  </nav>
</header>


<section class="main-content">
  <div class="container">
    <h2>Change First Name</h2>
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
        <label for="current_first_name">Current First Name:</label>
        <input type="text" id="current_first_name" name="current_first_name" required>
      </div>
      <div>
        <label for="new_first_name">New First Name:</label>
        <input type="text" id="new_first_name" name="new_first_name" required>
      </div>
      <div>
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
      </div>
      <div>
        <button type="submit" name="submit">Change First Name</button>
      </div>
    </form>
  </div>
</section>

<footer style="position: fixed; left: 0; bottom: 0; width: 100%;">
  <p>&copy; 2023 Library. All rights reserved.</p>
</footer>
</body>
</html>
