<?php
session_start();
include('connect.php');

if ($_SERVER['REQUEST_METHOD'] == "POST") {
  $username = $_POST['username'];
  $password = $_POST['password'];
  $select = "SELECT * FROM users WHERE username='$username' AND user_password='$password'";
  $result = mysqli_query($conn, $select);

  if (!$result) {
    echo "Query execution failed: " . mysqli_error($conn);
    exit;
  }

  if (mysqli_num_rows($result) > 0) {
    // Username and password are correct
    $user = mysqli_fetch_assoc($result);
    $userID = $user['User_ID'];
    $username = $user['username'];

    // Check if the user is an admin
    $adminQuery = "SELECT * FROM administrator WHERE User_ID='$userID'";
    $adminResult = mysqli_query($conn, $adminQuery);
    if (!$adminResult) {
      echo "Query execution failed: " . mysqli_error($conn);
      exit;
    }

    // Check if the user is a student
    $studentQuery = "SELECT * FROM students_professors WHERE User_ID='$userID'";
    $studentResult = mysqli_query($conn, $studentQuery);
    if (!$studentResult) {
      echo "Query execution failed: " . mysqli_error($conn);
      exit;
    }

    // Check if the user is an operator manager
    $operatorQuery = "SELECT * FROM school_unit_manager WHERE User_ID ='$userID'";
    $operatorResult = mysqli_query($conn, $operatorQuery);
    if (!$operatorResult) {
      echo "Query execution failed: " . mysqli_error($conn);
      exit;
    }

    if (mysqli_num_rows($adminResult) > 0) {
      // User is an admin
      $_SESSION['Admin_ID'] = $userID;
      $_SESSION['Manager_ID'] = ""; // Set an empty value for Manager_ID
      $_SESSION['username'] = $username;
      header("Location: admin.php");
      exit;
    } elseif (mysqli_num_rows($studentResult) > 0) {
      $_SESSION['studprof_ID'] = $userID;
      $_SESSION['Manager_ID'] = ""; // Set an empty value for Manager_ID
      $_SESSION['username'] = $username;
      header("Location: user_dashboard.php");
      exit;
    } elseif (mysqli_num_rows($operatorResult) > 0) {
      // User is an operator manager
      $_SESSION['studprof_ID'] = $userID;
      $_SESSION['Manager_ID'] = ""; // Set an empty value for Manager_ID
      $_SESSION['username'] = $username;
      header("Location: user_dashboard.php");
      exit;
    } else {
      // No matching role found
      echo "You need to register first.";
      exit;
    }
  } else {
    // Invalid username or password
    echo "Invalid username or password.";
    exit;
  }
}
?>



<!DOCTYPE html>
<html>
<head>
  <title>Login Page</title>
  <link rel="stylesheet" type="text/css" href="registration.css">
</head>
<body>
<div class="header">Library System</div>
<div class="container">
  <h2>Login</h2>
  <img src="EMP.png" class="top-right-image">

  <form action="login.php" method="POST">
    <div class="input-group">
      <label for="username">Username:</label>
      <input type="text" id="username" name="username" required>
    </div>
    <div class="input-group">
      <label for="password">Password:</label>
      <input type="password" id="password" name="password" required>
    </div>
    <div class="input-group">
      <input type="submit" value="Log in">
    </div>
  </form>
  
  <div class="input-group">
    <p>New user? <a href="registration.php">Register here</a></p>
  </div>
</div>
</body>
</html>
