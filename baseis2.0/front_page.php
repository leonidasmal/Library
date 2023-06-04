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


if (isset($_GET['keyword'])) {
  $keyword = $_GET['keyword'];

  // Construct the SQL statement with the search condition
  $selectSchools = "SELECT * FROM school_unit WHERE School_name LIKE '%$keyword%'";
} else {
  // Retrieve all the schools from the database
  $selectSchools = "SELECT * FROM school_unit";
}

$schoolsResult = mysqli_query($conn, $selectSchools);

if (!$schoolsResult) {
  echo "Query execution failed: " . mysqli_error($conn);
  exit;
}
?>


<!DOCTYPE html>
<html>
<head>
  <title>Front Page</title>
  
  <link rel="stylesheet" type="text/css" href="front_page.css">
  <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Arial&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Vast+Shadow&display=swap" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Open+Sans&display=swap" rel="stylesheet">

</head>
<body>
<h1 style="text-align: center; font-size: 36px; font-family: 'Vast Shadow', cursive; color: #333;">Library System</h1>

<nav>
  <ul>
    <li>
      <a href="admin_login.php" class="admin-login-button">
        <span class="material-icons" style="vertical-align: middle;">admin_panel_settings</span> Admin Login
      </a>
    </li>
    <li><a href="front_page.php" style="color: black;">Home</a></li>
    <li><a href="register_school.php" style="color: black;">Register a new School</a></li>
  </ul>
</nav>

<div class="container">
  <h2 style="font-family: 'Arial', sans-serif;">Available Schools</h2>
  
  <form method="GET" action="front_page.php" class="search-bar">
    <input type="text" name="search" placeholder="Search for a School" class="search-input">
    <button type="submit" class="search-button">Search</button>
  </form>
  
  <div class="school-list">
    <?php 
    $colors = array('red', 'blue', 'green', 'orange'); // Define an array of different colors
    $colorIndex = 0; // Initialize the color index
    while ($school = mysqli_fetch_assoc($schoolsResult)) { 
      // Check if the search query is set and the school name matches the search query
      $searchQuery = isset($_GET['search']) ? $_GET['search'] : '';
      if (empty($searchQuery) || stripos($school['School_name'], $searchQuery) !== false) { ?>
        <div class="school">
          <h3 style="font-family: 'Open Sans', sans-serif; color: <?php echo $colors[$colorIndex]; ?>;">
            <a href="login.php?School_ID=<?php echo $school['School_ID']; ?>" style="text-decoration: none;">
              <?php echo $school['School_name']; ?>
            </a>
          </h3>
        </div>
      <?php 
      $colorIndex = ($colorIndex + 1) % count($colors); // Update the color index for the next school
      }
    } ?>
  </div>
</div>

</body>
</html>
