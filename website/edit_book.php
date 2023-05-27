<?php
include("connect.php");
session_start();

// Check if Book_ID is present in the URL
if (isset($_GET['Book_ID'])) {
    $bookID = $_GET['Book_ID'];

    // Store the Book_ID in the session for later use if needed
    $_SESSION['Book_ID'] = $bookID;

    // Fetch the book details from the database based on the bookID
    $fetchBookQuery = "SELECT * FROM book WHERE Book_ID = ?";
    $fetchBookStmt = mysqli_prepare($conn, $fetchBookQuery);
    mysqli_stmt_bind_param($fetchBookStmt, 'i', $bookID);
    mysqli_stmt_execute($fetchBookStmt);
    $bookResult = mysqli_stmt_get_result($fetchBookStmt);

    // Check if the book exists
    if (mysqli_num_rows($bookResult) > 0) {
        $book = mysqli_fetch_assoc($bookResult);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $newtitle = $_POST['title'];
        $newpublisher = $_POST['publisher'];
        $newISBN = $_POST['ISBN'];
        $newpg_numbers = $_POST['pg_numbers'];
        $newsummary = $_POST['summary'];
        $newimage_URL = $_POST['image_URL'];
        $newlanguage = $_POST['language'];
        $newkeywords = $_POST['keywords'];
        $newAuthorNames = $_POST['new_authors'];
        $newcategories = $_POST['category'];
        $newavailablecopies = $_POST['newavailablecopies'];

        if (isset($_SESSION['School_ID'])) {
            $schoolID = $_SESSION['School_ID'];
        } else {
            echo "School_ID not provided";
            exit;
        }

        // Authors
        $existingAuthorIDs = array();
        foreach ($newAuthorNames as $author) {
            $authorExistsQuery = "SELECT Author_ID FROM author WHERE author_fullname = ?";
            $authorExistsStmt = mysqli_prepare($conn, $authorExistsQuery);
            mysqli_stmt_bind_param($authorExistsStmt, 's', $author);
            mysqli_stmt_execute($authorExistsStmt);
            $authorExistsResult = mysqli_stmt_get_result($authorExistsStmt);

            if (mysqli_num_rows($authorExistsResult) > 0) {
                $authorExistsRow = mysqli_fetch_assoc($authorExistsResult);
                $existingAuthorIDs[] = $authorExistsRow['Author_ID']; // Store existing author's ID
            }
        }

        // Check if the existing authors and newly inserted authors are the same
        $existingAuthorIDs = array_unique($existingAuthorIDs); // Remove duplicate author IDs
        sort($existingAuthorIDs); // Sort the existing author IDs in ascending order
        $newAuthorIDs = array_map('intval', $existingAuthorIDs); // Convert the existing author IDs to integers
        sort($newAuthorIDs); // Sort the new author IDs in ascending order

        if ($existingAuthorIDs === $newAuthorIDs) {
            // The existing authors and newly inserted authors are the same
            echo "No changes in authors. Update not required.";
            exit();
        }else {
            // Update the book_author table with the new author IDs
            $deleteAuthorsQuery = "DELETE FROM book_author WHERE Book_ID = ?";
            $deleteAuthorsStmt = mysqli_prepare($conn, $deleteAuthorsQuery);
            mysqli_stmt_bind_param($deleteAuthorsStmt, 'i', $bookID);
            mysqli_stmt_execute($deleteAuthorsStmt);
        
            $insertAuthorQuery = "INSERT INTO book_author (Book_ID, Author_ID) VALUES (?, ?)";
            $insertAuthorStmt = mysqli_prepare($conn, $insertAuthorQuery);
        foreach ($existingAuthorIDs as $newAuthorID) {
                mysqli_stmt_bind_param($insertAuthorStmt, 'ii', $bookID, $newAuthorID);
                mysqli_stmt_execute($insertAuthorStmt);
            }
             // Redirect to the book details page or display a success message
        header("Location: edit_book.php?Book_ID=" . $bookID);
        exit();
    }

        }
       
}
?>




<!DOCTYPE html>
<html>
<head>
  <title>Edit Book</title>
  <script>
    function addAuthorField() {
      var container = document.getElementById("authors-container");
      var input = document.createElement("input");
      input.type = "text";
      input.name = "author[]";
      container.appendChild(input);
    }
    function addCategoryField() {
      var container = document.getElementById("category-container");
      var input = document.createElement("input");
      input.type = "text";
      input.name = "category[]";
      container.appendChild(input);
    }
  </script>
</head>
<body>

<h1>Edit Book - Book ID: <?php echo $bookID; ?></h1>
  <h1>Edit Book</h1>
  <form method="POST" action="edit_book.php?School_ID=<?php echo $schoolID; ?>&Book_ID=<?php echo $bookID; ?>">
  <form method="POST" action="edit_book.php">
    <input type="hidden" name="Book_ID" value="<?php echo $bookID; ?>">
    <label>Title:</label>
    <input type="text" name="title" value="<?php echo $book['title']; ?>"><br><br>
    <label>Publisher:</label>
    <input type="text" name="publisher" value="<?php echo $book['publisher']; ?>"><br><br>
    <label>ISBN:</label>
    <input type="text" name="ISBN" value="<?php echo $book['ISBN']; ?>"><br><br>
    <label>Page Numbers:</label>
    <input type="text" name="pg_numbers" value="<?php echo $book['pg_numbers']; ?>"><br><br>
    <label>Summary:</label>
    <textarea name="summary"><?php echo $book['summary']; ?></textarea><br><br>
    <label>Image URL:</label>
    <input type="text" name="image_URL" value="<?php echo $book['image_URL']; ?>"><br><br>
    <label>Language:</label>
    <input type="text" name="language" value="<?php echo $book['Language_ID']; ?>"><br><br>
    <label>Keywords:</label>
    <br>
    <br>
    <label>Authors:</label>
    <?php
    // Fetch the authors of the book from the database
    $bookAuthorsQuery = "SELECT a.Author_ID, a.author_fullname FROM author a INNER JOIN book_author ba ON a.Author_ID = ba.Author_ID WHERE ba.Book_ID = ?";
    $bookAuthorsStmt = mysqli_prepare($conn, $bookAuthorsQuery);
    mysqli_stmt_bind_param($bookAuthorsStmt, 'i', $bookID);
    mysqli_stmt_execute($bookAuthorsStmt);
    $bookAuthorsResult = mysqli_stmt_get_result($bookAuthorsStmt);
    
    // Display the authors
    while ($author = mysqli_fetch_assoc($bookAuthorsResult)) {
        $authorID = $author['Author_ID'];
        $authorName = $author['author_fullname'];
        echo '<input type="text" name="new_authors[]" value="' . $authorName . '"><br>';
    }
    ?>
    <input type="text" name="new_authors[]" value="" placeholder="<?php echo mysqli_num_rows($bookAuthorsResult) > 0 ? 'Enter new author' : ''; ?>"><br>
    <br>
    <br>
    <label>Available Copies:</label>
    <input type="text" name="newavailablecopies" value="<?php echo $availableCopies; ?>"><br><br>
    <input type="submit" value="Update Book">
  </form>
</body>
</html>