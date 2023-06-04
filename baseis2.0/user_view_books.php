<?php
include("connect.php");
session_start();
unset($_SESSION['Book_ID']);
?>

<!DOCTYPE html>
<html>
<head>
  <title>School Details</title>
  <style>
    /* Modal styles */
    .modal {
      display: none;
      align-items: center;
      justify-content: center;
      position: fixed;
      z-index: 9999;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
    }

    .modal-content {
      position: relative;
      margin: auto;
      padding: 20px;
      border: 1px solid #888;
      width: 80%; /* Adjust the width as desired */
      max-width: 600px; /* Set a maximum width */
      box-shadow: 0 0 10px rgba(0, 0, 0, 0.3);
      border-radius: 5px;
      text-align: center; /* Center the content horizontally */
      overflow: auto; /* Enable scrolling within the modal if content overflows */
      background-color: #fff;
    }

    #editBookFrame {
      width: 100%;
      height: 80vh; /* Adjust the height as desired */
      border: none;
    }

    .close {
      color: #aaa;
      float: right;
      font-size: 28px;
      font-weight: bold;
      cursor: pointer;
    }

    .close:hover,
    .close:focus {
      color: black;
      text-decoration: none;
      cursor: pointer;
    }

    /* Additional styles for the iframe */
    #editBookFrame {
      width: 100%;
      height: 100%;
      border: none;
    }

    /* Adjustments to book layout */
    .book {
      display: flex;
      align-items: flex-start;
      margin-bottom: 20px;
    }

    .book-image {
      max-width: 150px;
      margin-right: 20px;
    }

    .book-image img {
      max-width: 100%;
      height: auto;
    }
  </style>

</head>
<body>

<?php

// Retrieve the search criteria
$searchTitle = isset($_POST['search_title']) ? $_POST['search_title'] : '';
$searchCategory = isset($_POST['search_category']) ? $_POST['search_category'] : '';
$searchAuthor = isset($_POST['search_author']) ? $_POST['search_author'] : '';



if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['Book_ID'])) {
  $_SESSION['Book_ID'] = $_POST['Book_ID'];
  header("Location: make_reservation.php"); // Redirect to make_reservation.php after storing the book ID in the session
  exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['R_Book_ID'])) {
  $_SESSION['Book_ID'] = $_POST['R_Book_ID'];
  header("Location: make_review.php"); // Redirect to make_review.php after storing the book ID in the session
  exit;
}
// Retrieve the school_id parameter
if (isset($_SESSION['School_ID'])) {
  $schoolID = $_SESSION['School_ID'];

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
  <form method="POST">
      <input type="hidden" name="School_ID" value="<?php echo $schoolID; ?>">
      <input type="text" name="search_title" placeholder="Search by Title" value="<?php echo $searchTitle; ?>">
      <input type="text" name="search_category" placeholder="Search by Category" value="<?php echo $searchCategory; ?>">
      <input type="text" name="search_author" placeholder="Search by Author" value="<?php echo $searchAuthor; ?>">
      <button type="submit">Search</button>
    </form>
  <?php
  // Query to fetch the books and their details
  $booksQuery = "SELECT * FROM book_details bdv INNER JOIN school_book sb ON sb.Book_ID = bdv.Book_ID WHERE sb.School_ID = ?";
  // Add search conditions based on the provided criteria
  if (!empty($searchTitle)) {
    $booksQuery .= " AND bdv.title LIKE '%$searchTitle%'";
  }

  if (!empty($searchCategory)) {
    $booksQuery .= " AND bdv.categories LIKE '%$searchCategory%'";
  }

  if (!empty($searchAuthor)) {
    $booksQuery .= " AND bdv.authors LIKE '%$searchAuthor%'";
  }
  
  
  $booksStmt = mysqli_prepare($conn, $booksQuery);
  mysqli_stmt_bind_param($booksStmt, 'i', $schoolID);
  mysqli_stmt_execute($booksStmt);
  $booksResult = mysqli_stmt_get_result($booksStmt);



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
        <form action="" method="post">
          <input type="hidden" name="R_Book_ID" value="<?php echo $book['Book_ID']; ?>">
          <button type="submit">Make a Review for this book</button>
        </form>
        <form action="" method="post">
          <input type="hidden" name="Book_ID" value="<?php echo $book['Book_ID']; ?>">
          <button type="submit">Make a Reservation</button>
        </form>

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

<form action="user_dashboard.php" method="get">
<input type="submit" value="Back">
</form>

</body>
</html>
