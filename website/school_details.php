<?php
include("connect.php");
session_start();


?>

<!DOCTYPE html>
<html>
<head>
  <title>School Details</title>
  <!DOCTYPE html>
<html>
<body>
<link rel="stylesheet" type="text/css" href="school_details.css">
<script>
    function deleteBook(bookID) {
      if (confirm("Are you sure you want to delete this book?")) {
        // Redirect to the delete book page with the book ID
        window.location.href = "delete_book.php?Book_ID=" + bookID;
      }
    }
    function editBook(bookID) {
  window.location.href = "edit_book.php?Book_ID=" + bookID;
}

  </script>
</head>
<body>
<?php
// Retrieve the school_id parameter
if (isset($_GET['School_ID'])) {
  $schoolID = $_GET['School_ID'];

  // Query to fetch the details of the selected school
  $schoolDetailsQuery = "SELECT * FROM school_unit WHERE School_ID = ?";
  $schoolDetailsStmt = mysqli_prepare($conn, $schoolDetailsQuery);
  mysqli_stmt_bind_param($schoolDetailsStmt, 'i', $schoolID);
  mysqli_stmt_execute($schoolDetailsStmt);
  $schoolDetailsResult = mysqli_stmt_get_result($schoolDetailsStmt);

  if (!$schoolDetailsResult) {
    echo "Query execution failed: " . mysqli_error($conn);
    exit;
  }
  
  // Fetch the school details
  $schoolDetails = mysqli_fetch_assoc($schoolDetailsResult);
?>
  <div class="school-details">
    <h2 class="primary-color">School Details</h2>
    <p><strong>School ID:</strong> <?php echo $schoolDetails['School_ID']; ?></p>
    <p><strong>School Name:</strong> <?php echo $schoolDetails['School_name']; ?></p>
    <p><strong>School Address:</strong> <?php echo $schoolDetails['address']; ?></p>
  </div>
 <?php
  // Query to fetch the books and their details
  $booksQuery = "SELECT * FROM book_details bdv INNER JOIN school_book sb ON sb.Book_ID = bdv.Book_ID WHERE sb.School_ID = '$schoolID'";
  $booksResult = mysqli_query($conn, $booksQuery);
  
  if (!$booksResult) {
    echo "Query execution failed: " . mysqli_error($conn);
    exit;
  }
?>

  <h2 class="primary-color">Books</h2>

  <?php
  // Display the books
  while ($book = mysqli_fetch_assoc($booksResult)) {
    $imageURL = $book['image_URL'];
    ?>
    <div class="book">
      <div class="book-image">
        <?php if (!empty($imageURL)) { ?>
          <img src="<?php echo $imageURL; ?>" alt="Book Image">
        <?php } ?>
      </div>
      <div class="book-details">
        <h3 class="book-title secondary-color"><?php echo $book['title']; ?></h3>
        <div class="book-info">
          <p><strong>Number of pages:</strong> <?php echo $book['pg_numbers']; ?></p>
          <p><strong>Language:</strong> <?php echo $book['language_name']; ?></p>
          <p><strong>Author:</strong> <?php echo $book['authors']; ?></p>
          <p><strong>Category:</strong> <?php echo $book['categories']; ?></p>
        </div>
        <div class="action-buttons">
        <button onclick="editBook(<?php echo $book['Book_ID']; ?>)">Edit</button>
                  <button onclick="deleteBook(<?php echo $book['Book_ID']; ?>)">Delete</button>
        </div>

      </div>
    </div>
    <hr>
<?php
  }
} else {
  echo "Invalid school ID";
}
?>
<a href="add_book.php?School_ID=<?php echo $schoolID; ?>">Add a Book</a>
</body>
</html>