<?php
session_start();
include('connect.php');
ini_set('display_errors', 1);
error_reporting(E_ALL);

if ($_SERVER['REQUEST_METHOD'] == "POST") {
  // Retrieve the submitted username and password
  $username = $_POST['username'];
  $password = $_POST['password'];
  // Query to check if the login credentials are valid
  $select = "SELECT * FROM users WHERE username='$username' AND user_password='$password'";
  $result = mysqli_query($conn, $select);

  if (!$result) {
    echo "Query execution failed: " . mysqli_error($conn);
    exit;
  }

  // Check if the login credentials are correct
  if (mysqli_num_rows($result) > 0) {
    $user = mysqli_fetch_assoc($result);
    $userID = $user['User_ID'];

    // Retrieve the School_ID associated with the user in the school_unit_operator table
    $operatorQuery = "SELECT School_ID FROM school_unit_manager WHERE User_ID='$userID'";
    $operatorResult = mysqli_query($conn, $operatorQuery);

    if (!$operatorResult) {
      echo "Query execution failed: " . mysqli_error($conn);
      exit;
    }

    // Retrieve the School_ID associated with the user in the studprofessors table
    $studprofQuery = "SELECT School_ID FROM students_professors WHERE User_ID='$userID'";
    $studprofResult = mysqli_query($conn, $studprofQuery);

    if (!$studprofResult) {
      echo "Query execution failed: " . mysqli_error($conn);
      exit;
    }

    // Check if the selected School_ID matches the user's School_ID in either table
    if (isset($_SESSION['School_ID'])) {
      $selectedSchoolID = $_SESSION['School_ID'];

      if (mysqli_num_rows($operatorResult) > 0 && $selectedSchoolID == mysqli_fetch_assoc($operatorResult)['School_ID']) {
        // User is an operator manager
        $_SESSION['Manager_ID'] = $userID;
        $_SESSION['School_ID'] = $selectedSchoolID;
        $_SESSION['username'] = $username;
        header("Location: operator_manager_dashboard.php");
        exit;
      } elseif (mysqli_num_rows($studprofResult) > 0 && $selectedSchoolID == mysqli_fetch_assoc($studprofResult)['School_ID']) {
        // User is a student or professor
        $_SESSION['studprof_ID'] = $userID;
        $_SESSION['School_ID'] = $selectedSchoolID;
        $_SESSION['username'] = $username;
        header("Location: user_dashboard.php");
        exit;
      } else {
        echo "You are not registered in this school.";
        exit;
      }
    } else {
      echo "School ID is not set.";
      exit;
    }
  } else {
    echo "Invalid username or password.";
    exit;
  }
} elseif (isset($_GET['School_ID'])) {
  $_SESSION['School_ID'] = $_GET['School_ID'];
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
<img src="https://t4.ftcdn.net/jpg/02/29/75/83/360_F_229758328_7x8jwCwjtBMmC6rgFzLFhZoEpLobB6L8.jpg" alt="Image Description">

  <h2>Login</h2>
  <img src="EMP.png" class="top-right-image">

  <form action="<?php echo $_SERVER['PHP_SELF']; ?>" method="POST">
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
