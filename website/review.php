<?php
include("connect.php");
session_start();
$bookID=1;
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Retrieve the review details from the form
  $review = $_POST['review'];
  $bookID=1;

  // Insert the review into the database
  $insertReviewQuery = "INSERT INTO reviews (Book_ID, review) VALUES (?, ?)";
  $insertReviewStmt = mysqli_prepare($conn, $insertReviewQuery);
  mysqli_stmt_bind_param($insertReviewStmt, 'is', $bookID, $review);
  mysqli_stmt_execute($insertReviewStmt);

  if (mysqli_stmt_affected_rows($insertReviewStmt) > 0) {
    // Review inserted successfully
    echo "Review added successfully.";
  } else {
    echo "Failed to add review.";
  }
}

// Retrieve the book ID from the query parameter
if (isset($_GET['Book_ID'])) {
  $bookID = $_GET['Book_ID'];

  // Query to fetch the book details
  $bookQuery = "SELECT * FROM book_details WHERE Book_ID = ?";
  $bookStmt = mysqli_prepare($conn, $bookQuery);
  mysqli_stmt_bind_param($bookStmt, 'i', $bookID);
  mysqli_stmt_execute($bookStmt);
  $bookResult = mysqli_stmt_get_result($bookStmt);

  if (!$bookResult) {
    echo "Query execution failed: " . mysqli_error($conn);
    exit;
  }

  // Fetch the book details
  $book = mysqli_fetch_assoc($bookResult);
} else {
  echo "Invalid book ID.";
  exit;
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>School Details</title>
  <link rel="stylesheet" type="text/css" href="school_details.css">
</head>
<body>
<!-- Existing code for school details -->

<div class="school-details">
  <h2 class="primary-color">School Details</h2>
  <!-- Display school details -->
</div>

<h2 class="primary-color">Book Details</h2>
<div class="book-details">
  <!-- Display book details -->
</div>

<!-- Add Review Form -->
<h2 class="primary-color">Add Review</h2>
<form method="POST" action="">
  <input type="hidden" name="bookID" value="<?php echo $bookID; ?>">
  <textarea name="review" placeholder="Enter your review"></textarea>
  <button type="submit">Add Review</button>
</form>

<!-- Existing code for displaying books and reviews -->
</body>
</html>