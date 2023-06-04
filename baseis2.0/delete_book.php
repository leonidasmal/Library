<?php
include("connect.php");
session_start();

if (isset($_GET['Book_ID'])) {
  $bookID = $_GET['Book_ID'];
  if (isset($_SESSION['School_ID'])) {
    $schoolID = $_SESSION['School_ID'];
  } else {
    echo "School_ID not provided";
    exit;
  }

   
 
   $deleteSchoolBookQuery = "DELETE FROM school_book WHERE Book_ID = ?";
   $deleteSchoolBookStmt = mysqli_prepare($conn, $deleteSchoolBookQuery);
   mysqli_stmt_bind_param($deleteSchoolBookStmt, 'i', $bookID);
   mysqli_stmt_execute($deleteSchoolBookStmt);
  

  // Execute the delete statement
  if (mysqli_stmt_execute($deleteSchoolBookStmt)) {
    // Redirect to the book list page or display a success message
     // Redirect to the school details page
     header("Location: school_details.php?School_ID=" . $schoolID);
     exit();
  } else {
    // Handle the deletion failure
    echo "Failed to delete the book: " . mysqli_error($conn);
    exit();
  }
} else {
  echo "Invalid book ID";
}
?>
