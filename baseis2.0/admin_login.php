<?php
session_start();
include('connect.php');

$errorMessage = ""; // Initialize the error message variable

if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $username = $_POST['username'];
  $password = $_POST['password'];
  $_SESSION["username"] = $username;

  // Check if the login is for a regular user
  $userQuery = "SELECT User_ID FROM users WHERE username='$username' AND user_password='$password'";
  $userResult = mysqli_query($conn, $userQuery);

  if (!$userResult) {
    $errorMessage = "Query execution failed: " . mysqli_error($conn);
  }

  if (mysqli_num_rows($userResult) > 0) {
    // User login successful
    $row = mysqli_fetch_assoc($userResult);
    $userId = $row['User_ID'];

    // Check if the user ID matches an admin in the administrator table
    $adminQuery = "SELECT Admin_ID FROM administrator WHERE User_ID='$userId'";
    $adminResult = mysqli_query($conn, $adminQuery);

    if (!$adminResult) {
      $errorMessage = "Query execution failed: " . mysqli_error($conn);
    }

    if (mysqli_num_rows($adminResult) > 0) {
      // Admin login successful
      $adminRow = mysqli_fetch_assoc($adminResult);
      $adminId = $adminRow['Admin_ID'];

      $_SESSION['admin'] = true;
      $_SESSION['Admin_ID'] = $adminId; // Save the Admin_ID in the session

      header("Location: admin_dashboard.php"); // Redirect to the admin_dashboard.php page
      exit;
    } else {
      // User is not an admin
      $errorMessage = "Error: You are not an admin.";
    }
  } else {
    // Invalid username or password
    $errorMessage = "Error: Invalid username or password.";
  }
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>Login</title>
  <style>
  body {
      background-color:  #000; padding: 20px;
      font-family: Arial, sans-serif;
    }
    .container {
      max-width: 400px;
      margin: 0 auto;
      background-color:  #FFFFFF;
      padding: 20px;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
      text-align: left; /* Center the contents */
    }
    .container h2 {
      text-align: center;
      margin-bottom: 10px;
      color: #333;
    }
    .form-group {
        font-family: "Your Custom Font", Arial, sans-serif; /* Specify your desired font family */
      margin-bottom: 20px;
      text-align: left; 
    }
    .form-group label {
      display: block;
      font-weight: bold;
      margin-bottom: 5px;
      color: #555;
    }
    .form-group input {
      width: 350px;
      padding: 10px;
      border: 1px solid #ccc;
      border-radius: 3px;
      font-size: 14px;
    }
    .form-group input[type="submit"] {
      background-color: #000;
      display: block;
      margin: 0 auto;
      /* Change the color to your desired color */
      color: #fff;
      cursor: pointer;
    }
    .form-group input[type="submit"]:hover {
      background-color: #000;
    }
    .error-message {
      color: #ff0000;
      font-size: 14px;
      margin-top: 10px;
    }
    .logo {
      text-align: center;
      margin-bottom: 10px;
    }
    .logo img {
      max-width: 300px;
    }
    .nav {
      background-color: #000;
      padding: 10px;
      margin-bottom: 20px;
    }

    .nav ul {
      list-style-type: none;
      margin: 0;
      padding: 0;
      overflow: hidden;
    }

    .nav li {
      float: left;
    }

    .nav li a {
      display: block;
      color: #fff;
      text-align: center;
      padding: 14px 16px;
      text-decoration: none;
      font-size: 30px; 
    }

    .nav li a:hover {
      background-color: #111;
    }
  </style>
</head>
<body>
  <div class="nav">
    <ul>
    <li><a href="front_page.php"><h3>Home</h3></a></li>      
    </ul>
  </div>

</style>
</head>

<body>
<div class="container">
    <div class="logo">
      <img src="https://media.istockphoto.com/id/1192884194/vector/admin-sign-on-laptop-icon-stock-vector.jpg?s=170667a&w=0&k=20&c=S274xvXNsp27UyKxzNjhmZEzAb3Zqi2pFOqZjLsZJz0=" alt="Logo">
    </div>
    <h2>Welcome to the Admin Login</h2>
    <form action="admin_login.php" method="POST">
      <div class="form-group">
        <label for="username">Username:</label>
        <input type="text" id="username" name="username" required>
      </div>
      <div class="form-group">
        <label for="password">Password:</label>
        <input type="password" id="password" name="password" required>
      </div>
      <div class="form-group">
        <input type="submit" value="Log in">
      </div>
    </form>
    <div class="error-message">
      <?php
      if (!empty($errorMessage)) {
        echo $errorMessage;
      }
      ?>
    </div>
  </div>
</body>
</html>