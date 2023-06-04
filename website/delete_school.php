<?php
include("connect.php");
session_start();

if (isset($_SESSION['School_ID'])) {
  $schoolID = $_SESSION['School_ID'];
  var_dump($schoolID);
  
  echo '<input type="hidden" name="schoolID" value="'.$schoolID.'">';
  // Fetch the school's name from the database
  $schoolNameQuery = "SELECT school_name FROM school_unit WHERE School_ID = $schoolID";
  $result = mysqli_query($conn, $schoolNameQuery);
  $schoolName = mysqli_fetch_assoc($result)['school_name'];

  if (isset($_POST['confirmDelete'])) {

    // Disable foreign key checks
    $disableForeignKeyQuery = "SET FOREIGN_KEY_CHECKS = 0";
    mysqli_query($conn, $disableForeignKeyQuery);

    // Start a transaction
    mysqli_begin_transaction($conn);

    // Delete the associated rows using appropriate DELETE statements
    $deleteQueries = [
      "DELETE FROM school_book WHERE School_ID = $schoolID",
      "DELETE FROM borrower_card WHERE studprof_id IN (SELECT studprof_id FROM students_professors WHERE School_ID = $schoolID)",
      "DELETE FROM loan WHERE studprof_id IN (SELECT studprof_id FROM students_professors WHERE School_ID = $schoolID)",
      "DELETE FROM reservation WHERE studprof_id IN (SELECT studprof_id FROM students_professors WHERE School_ID = $schoolID)",
      "DELETE FROM students_professors WHERE School_ID = $schoolID",
      "DELETE FROM school_unit_manager WHERE School_ID = $schoolID",
      "DELETE FROM users WHERE user_ID IN (SELECT user_ID FROM students_professors WHERE School_ID = $schoolID)",
      "DELETE FROM school_unit WHERE school_ID = $schoolID"
    ];

    $deletionErrors = false;

    // Execute the DELETE statements
    foreach ($deleteQueries as $deleteQuery) {
      if (!mysqli_query($conn, $deleteQuery)) {
        $deletionErrors = true;
        break;
      }
    }

    if (!$deletionErrors) {
      // Commit the transaction
      mysqli_commit($conn);
      header("Location: register_schools.php");
    } else {
      // Rollback the transaction
      mysqli_rollback($conn);
      echo "Error deleting rows: " . mysqli_error($conn);
    }

    // Re-enable foreign key checks
    $enableForeignKeyQuery = "SET FOREIGN_KEY_CHECKS = 1";
    mysqli_query($conn, $enableForeignKeyQuery);
  } elseif (isset($_POST['cancelDelete'])) {
    echo "Deletion canceled. School was not deleted.";
    header("Location: register_schools.php");
    exit;
  }
} else {
  echo "School_ID not provided";
  exit;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin_dashboard</title>
  <link rel="stylesheet" type="text/css" href="admin_dashboard.css">
  <style>
    footer {
     
     text-align: left;
    margin-top: 650px;
  }
  </style>
</head>

<body>
<header>
  <div class="logo">Library</div>
  <nav>
    <ul>
    <li><a href="admin_dashboard.php">Home</a></li>
    <li><a href="view_account.php">My Account</a></li> <!-- Updated link -->
      <li><a href="#">Library Events</a></li>
      <li><a href="front_page.php">Log Out</a></li> <!-- Add logout link with query parameter -->    </ul>
  
  </nav>
</header>

<div style="text-align: center;">
  <p style="font-size: 30px; font-weight: bold;">Are you sure you want to delete the school "<?php echo $schoolName; ?>"? This action cannot be undone and will <span style="color: red;">permanently delete all associated students, professors, and operators.</span></p>
  <form method="POST" action="">
    <input type="hidden" name="schoolID" value="<?php echo $schoolID; ?>">
    <input type="submit" name="confirmDelete" value="Confirm" style="background-color: red; color: white; padding: 5px 10px; margin-right: 10px;">
    <input type="submit" name="cancelDelete" value="Cancel" style="background-color: gray; color: white; padding: 5px 10px;">
  </form>
</div>

<footer>
  <p>&copy; 2023 Library. All rights reserved.</p>
</footer>
</body>
</html>
