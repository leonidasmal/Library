<?php
session_start();
include('connect.php');

// Retrieve all the schools from the database
$selectSchools = "SELECT * FROM school_unit";
$schoolsResult = mysqli_query($conn, $selectSchools);

if (!$schoolsResult) {
  echo "Query execution failed: " . mysqli_error($conn);
  exit;
  
}
if (isset($_GET['School_ID'])) {
  $_SESSION['School_ID'] = $_GET['School_ID'];
}
?>


<!DOCTYPE html>
<html>
<head>
  <title>Front Page</title>
  <link rel="stylesheet" type="text/css" href="frontpage.css">
</head>
<body>
<div class="header">Library System</div>
<div class="container">
  <h2>Schools</h2>

  <div class="school-list">
    <?php while ($school = mysqli_fetch_assoc($schoolsResult)) { ?>
      <div class="school">
        <h3><?php echo $school['School_name']; ?></h3>
        <a href="login.php?School_ID=<?php echo $school['School_ID']; ?>">Log in to this school</a>
      </div>
    <?php } ?>
  </div>
  <div class="admin-login">
    <h2>Admin Login</h2>
    <a href="admin_login.php" class="admin-login-button">Login as Admin</a>
  </div>
</div>
</body>
</html>
