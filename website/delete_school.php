<?php
include("connect.php");
session_start();

if (isset($_SESSION['School_ID'])) {
  $schoolID = $_SESSION['School_ID'];

  // Perform the deletion
  // Delete the school and associated rows from the tables

  // Display a confirmation message to the user
  echo "Are you sure you want to delete the school? This action cannot be undone and will permanently delete all associated students, professors, and operators.<br>";
  echo '<form method="POST" action="">';
  echo '<input type="hidden" name="schoolID" value="'.$schoolID.'">';
  echo '<input type="submit" name="confirmDelete" value="Yes">';
  echo '<input type="submit" name="cancelDelete" value="No">';
  echo '</form>';

  if (isset($_POST['confirmDelete'])) {
    $schoolID = $_POST['schoolID'];

    $deleteQuery = "DELETE FROM school_book WHERE School_ID = $schoolID";
    $deleteResult = mysqli_query($conn, $deleteQuery);

    $deleteQuery = "DELETE FROM borrower_card WHERE studprof_id IN 
                    (SELECT studprof_id FROM students_professors WHERE School_ID = $schoolID)";
    $deleteResult = mysqli_query($conn, $deleteQuery);

    $deleteQuery = "DELETE FROM students_professors WHERE School_ID = $schoolID";
    $deleteResult = mysqli_query($conn, $deleteQuery);

    $deleteQuery = "DELETE FROM school_unit_manager WHERE School_ID = $schoolID";
    $deleteResult = mysqli_query($conn, $deleteQuery);

    if ($deleteResult) {
      echo "School and associated rows deleted successfully.";

      // Delete entry from the register table
      $deleteRegisterQuery = "DELETE FROM register WHERE school_id = $schoolID";
      $deleteRegisterResult = mysqli_query($conn, $deleteRegisterQuery);

      if ($deleteRegisterResult) {
        echo "School and associated registration deleted successfully.";
      } else {
        echo "Error deleting registration: " . mysqli_error($conn);
      }
    } else {
      echo "Error deleting rows: " . mysqli_error($conn);
    }
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
