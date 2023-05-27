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

   // Prepare the delete queries
   $deleteAuthorQuery = "DELETE FROM book_author WHERE Book_ID = ?";
   $deleteAuthorStmt = mysqli_prepare($conn, $deleteAuthorQuery);
   mysqli_stmt_bind_param($deleteAuthorStmt, 'i', $bookID);
   mysqli_stmt_execute($deleteAuthorStmt);
 
   $deleteSchoolBookQuery = "DELETE FROM school_book WHERE Book_ID = ?";
   $deleteSchoolBookStmt = mysqli_prepare($conn, $deleteSchoolBookQuery);
   mysqli_stmt_bind_param($deleteSchoolBookStmt, 'i', $bookID);
   mysqli_stmt_execute($deleteSchoolBookStmt);
 
   $deleteSchoolcategoryQuery = "DELETE FROM book_category WHERE Book_ID = ?";
   $deleteSchoolcategoryStmt = mysqli_prepare($conn, $deleteSchoolcategoryQuery);
   mysqli_stmt_bind_param($deleteSchoolcategoryStmt, 'i', $bookID);
   mysqli_stmt_execute($deleteSchoolcategoryStmt);
 
   $deleteSchoolkeywordQuery = "DELETE FROM book_keyword WHERE Book_ID = ?";
   $deleteSchoolkeywordStmt = mysqli_prepare($conn, $deleteSchoolkeywordQuery);
   mysqli_stmt_bind_param($deleteSchoolkeywordStmt, 'i', $bookID);
   mysqli_stmt_execute($deleteSchoolkeywordStmt);
 
   $deleteBookQuery = "DELETE FROM book WHERE Book_ID = ?";
   $deleteBookStmt = mysqli_prepare($conn, $deleteBookQuery);
   mysqli_stmt_bind_param($deleteBookStmt, 'i', $bookID);
 
   // Execute the delete statements
   mysqli_stmt_execute($deleteBookStmt);


  
  // Bind the book ID as a parameter
  mysqli_stmt_bind_param($deleteBookStmt, 'i', $bookID);

  // Execute the delete statement
  if (mysqli_stmt_execute($deleteBookStmt)) {
    // Redirect to the book list page or display a success message
     // Redirect to the school details page
     header("Location: school_details.php?School_ID=" . $schoolID);
     exit();
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
