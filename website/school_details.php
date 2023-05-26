<?php
include("connect.php");
session_start();
?>

<!DOCTYPE html>
<html>
<head>
  <title>School Details</title>
  <style>
    body {
      font-family: Arial, sans-serif;
      background-color: #f5f5f5;
      margin: 0;
      padding: 20px;
    }

    h2 {
      color: #333;
      margin-bottom: 10px;
    }

    p {
      margin: 0;
    }

    .school-details {
      background-color: #fff;
      padding: 20px;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .book {
      background-color: #fff;
      padding: 20px;
      margin-top: 20px;
      border-radius: 5px;
      box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
    }

    .book-title {
      font-size: 18px;
      font-weight: bold;
      color: #333;
    }

    .book-details {
      margin-top: 10px;
    }

    .book-details p {
      margin-bottom: 5px;
    }

    .book-details hr {
      margin: 10px 0;
      border: none;
      border-top: 1px solid #ccc;
    }

    .primary-color {
      color: #3498db;
    }

    .secondary-color {
      color: #e67e22;
    }
  </style>
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
  $booksQuery = "SELECT * FROM book_details_view  bdv 
  INNER JOIN school_book sb ON sb.Book_ID =bdv.Book_ID WHERE sb.School_ID=' $schoolID'";
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
?>
    <div class="book">
      <h3 class="book-title secondary-color"><?php echo $book['title']; ?></h3>
      <div class="book-details">
        <p><strong>Book ID:</strong> <?php echo $book['book_id']; ?></p>
        <p><strong>Number of pages:</strong> <?php echo $book['pg_numbers']; ?></p>
        <p><strong>Language:</strong> <?php echo $book['language_name']; ?></p>
        <p><strong>Author:</strong> <?php echo $book['authors']; ?></p>
        <p><strong>Category:</strong> <?php echo $book['categories']; ?></p>

      </div>
      <hr>
    </div>
<?php
  }
} else {
  echo "Invalid school ID";
}
?>
</body>
</html>
