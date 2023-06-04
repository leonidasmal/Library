<?php
include('connect.php');
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
  header("Location: front_page.php");
  exit;
}

// Logout functionality
if (isset($_GET['logout'])) {
  session_destroy(); // Destroy all session data
  header("Location: front_page.php");
  exit;
}

// Retrieve the user's account information
$username = $_SESSION['username'];
$userID = $_SESSION['User_ID'];
// Modify the query based on your user table structure


// Check if the user is a professor
$professorQuery = "SELECT * FROM students_professors WHERE user_ID = '{$userID}' AND Is_Professor = 1";
$professorResult = mysqli_query($conn, $professorQuery);
$isProfessor = mysqli_num_rows($professorResult) > 0;

// Check if the user is a unit manager
$managerQuery = "SELECT * FROM school_unit_manager WHERE User_ID = '{$userID}'";
$managerResult = mysqli_query($conn, $managerQuery);
$isManager = mysqli_num_rows($managerResult) > 0;

// Check if the user is a unit manager
$ADMINQuery = "SELECT * FROM administrator WHERE User_ID = '{$userID}'";
$ADMINResult = mysqli_query($conn, $ADMINQuery);
$isAdmin = mysqli_num_rows($ADMINResult) > 0;

// Retrieve the user's school, first name, and last name OF THE USER 
$SPuserDetailsQuery = "SELECT users.first_name, users.last_name, school_unit.School_Name,users.Email
                    FROM users
                    INNER JOIN students_professors ON users.User_ID = students_professors.User_ID
                    INNER JOIN school_unit ON school_unit.School_ID = students_professors.School_ID
                    WHERE users.User_ID = '{$userID}'";

$SPuserDetailsResult = mysqli_query($conn, $SPuserDetailsQuery);
$SPuserDetails = mysqli_fetch_assoc($SPuserDetailsResult );
// Retrieve the user's school, first name, and last name OF THE MANAGER
$MANAGERuserDetailsQuery = "SELECT users.first_name, users.last_name, school_unit.School_Name,users.Email
                    FROM users
                    INNER JOIN school_unit_manager  sum ON users.User_ID = sum.User_ID
                    INNER JOIN school_unit ON school_unit.School_ID = sum.School_ID
                    WHERE users.User_ID = '{$userID}'";
$MANAGERuserDetailsResult = mysqli_query($conn, $MANAGERuserDetailsQuery);
$MANAGERuserDetails = mysqli_fetch_assoc($MANAGERuserDetailsResult);
// Retrieve the user's school, first name, and last name OF THE ADMIN
$ADMINuserDetailsQuery = "SELECT users.first_name, users.last_name, users.Email
                    FROM users
                    WHERE users.User_ID = '{$userID}'";
$ADMINuserDetailsResult = mysqli_query($conn, $ADMINuserDetailsQuery);
$ADMINuserDetails = mysqli_fetch_assoc($ADMINuserDetailsResult);

?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>User Account</title>
  <link rel="stylesheet" type="text/css" href="user_dash.css">
  
  </head>
  <style>
    footer {
     
     text-align: left;
    margin-top: 650px;
  }
  </style>
<body>
<header>
  <div class="logo">Library</div>
  <nav>
  <ul>
        <?php if (!$isManager && !$isAdmin) { ?>
          <li><a href="user_dashboard.php">Home</a></li>
        <?php } elseif(!$isAdmin) { ?>
          <li><a href="operator_manager_dashboard.php">Home</a></li>
        <?php } else { ?>
          <li><a href="admin_dashboard.php">Home</a></li>
          <?php } ?>
        
        <?php if (!$isManager && !$isAdmin) { ?>
          <li><a href="search_book.php">Search Books</a></li>
        <?php } ?>
      <li><a href="view_account.php">My Account</a></li> <!-- Updated link -->
      <li><a href="?logout">Log Out</a></li> <!-- Add logout link with query parameter -->   
    </ul>
  </nav>
</header>
<section class="main-content">
  <div class="container">
    <h2>Account Details</h2>
    <div class="account-details">
      <div class="card">
        <h3>Username: <?php echo $username; ?></h3>
        <?php if (!$isManager && !$isAdmin) { ?>
          <h3>First Name: <?php echo $SPuserDetails['first_name']; ?></h3>
          <h3>Last Name: <?php echo $SPuserDetails['last_name']; ?></h3>
          <h3>Email: <?php echo $SPuserDetails['Email']; ?></h3>
          <h3>School Name: <?php echo $SPuserDetails['School_Name']; ?></h3>
        <?php } elseif(!$isAdmin) { ?>
          <h3>First Name: <?php echo $MANAGERuserDetails['first_name']; ?></h3>
          <h3>Last Name: <?php echo $MANAGERuserDetails['last_name']; ?></h3>
          <h3>Email: <?php echo $MANAGERuserDetails['Email']; ?></h3>
          <h3>School Name: <?php echo $MANAGERuserDetails['School_Name']; ?></h3>
        <?php } else { ?>
          <h3>First Name: <?php echo $ADMINuserDetails['first_name']; ?></h3>
          <h3>Last Name: <?php echo $ADMINuserDetails['last_name']; ?></h3>
          <h3>Email: <?php echo $ADMINuserDetails['Email']; ?></h3>
        <?php } ?>
      </div>
      <div class="card">
        <h3>Change Password</h3>
        <a href="change_password.php">Edit Password</a>
      </div>
      <?php if ($isProfessor || $isManager || $isAdmin) { ?>
        <div class="card">
          <h3>Edit Username</h3>
          <a href="change_username.php">Edit username</a>
        </div>
      <?php } ?>
      <?php if ($isProfessor || $isManager || $isAdmin) { ?>
        <div class="card">
          <h3>Edit First Name</h3>
          <a href="change_firstname.php">Edit your First name</a>
        </div>
      <?php } ?>
      <?php if ($isProfessor || $isManager || $isAdmin) { ?>
        <div class="card">
          <h3>Edit your Last name</h3>
          <a href="change_lastname.php">Edit Last Name</a>
        </div>
      <?php } ?>
      <?php if ($isProfessor || $isManager || $isAdmin) { ?>
        <div class="card">
          <h3>Edit your email</h3>
          <a href="change_email.php">Edit Email</a>
        </div>
      <?php } ?>
    </div>
  </div>
</section>

<footer>
  <p>&copy; 2023 Library. All rights reserved.</p>
</footer>
</body>
</html>

<?php

mysqli_close($conn);
?>
