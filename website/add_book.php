<?php
include("connect.php");
session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  // Retrieve the book details from the form submission
  $title = $_POST['title'];
  $publisher = $_POST['publisher'];
  $ISBN = $_POST['ISBN'];
  $pg_numbers = $_POST['pg_numbers'];
  $summary = $_POST['summary'];
  $image_URL = $_POST['image_URL'];
  $language = $_POST['language'];
  $keywords = $_POST['keyword'];
  $authors = $_POST['author']; 
  $categories = $_POST['category']; 
  $availablecopies = $_POST['availablecopies']; 


  if (isset($_SESSION['School_ID'])) {
    $schoolID = $_SESSION['School_ID'];
  } else {
    echo "School_ID not provided";
    exit;
  }

  
  
//Authors
$existingAuthors = array();
$newAuthors = array();
$insertedAuthorIDs = array();
$existingAuthorIDs = array();
foreach ($authors as $author) {
  $authorExistsQuery = "SELECT Author_ID FROM author WHERE author_fullname = ?";
  $authorExistsStmt = mysqli_prepare($conn, $authorExistsQuery);
  mysqli_stmt_bind_param($authorExistsStmt, 's', $author);
  mysqli_stmt_execute($authorExistsStmt);
  $authorExistsResult = mysqli_stmt_get_result($authorExistsStmt);

  if (mysqli_num_rows($authorExistsResult) > 0) {
    $authorExistsRow = mysqli_fetch_assoc($authorExistsResult);
    $existingAuthorIDs[] = $authorExistsRow['Author_ID']; // Store existing author's ID
  } else {
    $newAuthors[] = $author;
  }
}
// Insert the new authors into the author table
if (!empty($newAuthors)) {
  foreach ($newAuthors as $author) {
    $insertAuthorQuery = "INSERT INTO author (author_fullname) VALUES (?)";
    $selectAuthorIDQuery = "SELECT Author_ID FROM author WHERE author_fullname = ?";
    $insertAuthorStmt = mysqli_prepare($conn, $insertAuthorQuery);
    $selectAuthorIDStmt = mysqli_prepare($conn, $selectAuthorIDQuery);

    mysqli_stmt_bind_param($insertAuthorStmt, 's', $author);
    mysqli_stmt_execute($insertAuthorStmt);

    // Retrieve the author ID of the last inserted author
    mysqli_stmt_bind_param($selectAuthorIDStmt, 's', $author);
    mysqli_stmt_execute($selectAuthorIDStmt);
    $selectAuthorIDResult = mysqli_stmt_get_result($selectAuthorIDStmt);
    $selectAuthorIDRow = mysqli_fetch_assoc($selectAuthorIDResult);
    $authorID = $selectAuthorIDRow['Author_ID'];

    $insertedAuthorIDs[] = $authorID; // Add the author ID to the array
  }
}
$allAuthorIDs = array_merge($existingAuthorIDs, $insertedAuthorIDs);


// categories
$existingCategories = array();
$newCategories = array();
$insertedCategoryIDs = array();
$existingCategoryIDs = array();

foreach ($categories as $category) {
  $categoryExistsQuery = "SELECT Category_ID FROM category WHERE category = ?";
  $categoryExistsStmt = mysqli_prepare($conn, $categoryExistsQuery);
  mysqli_stmt_bind_param($categoryExistsStmt, 's', $category);
  mysqli_stmt_execute($categoryExistsStmt);
  $categoryExistsResult = mysqli_stmt_get_result($categoryExistsStmt);

  if (mysqli_num_rows($categoryExistsResult) > 0) {
    $categoryExistsRow = mysqli_fetch_assoc($categoryExistsResult);
    $existingCategoryIDs[] = $categoryExistsRow['Category_ID']; // Store existing category's ID
  } else {
    $newCategories[] = $category;
  }
}
// Insert the new categories into the category table
if (!empty($newCategories)) {
  $insertCategoryQuery = "INSERT INTO category (category) VALUES (?)";
  $selectCategoryIDQuery = "SELECT Category_ID FROM category WHERE category = ?";
  $insertCategoryStmt = mysqli_prepare($conn, $insertCategoryQuery);
  $selectCategoryIDStmt = mysqli_prepare($conn, $selectCategoryIDQuery);

  foreach ($newCategories as $category) {
    mysqli_stmt_bind_param($insertCategoryStmt, 's', $category);
    mysqli_stmt_execute($insertCategoryStmt);

    // Retrieve the category ID of the last inserted category
    mysqli_stmt_bind_param($selectCategoryIDStmt, 's', $category);
    mysqli_stmt_execute($selectCategoryIDStmt);
    $selectCategoryIDResult = mysqli_stmt_get_result($selectCategoryIDStmt);
    $selectCategoryIDRow = mysqli_fetch_assoc($selectCategoryIDResult);
    $categoryID = $selectCategoryIDRow['Category_ID'];

    $insertedCategoryIDs[] = $categoryID; // Add the category ID to the array
  }
}
$allCategoryIDs = array_merge($existingCategoryIDs, $insertedCategoryIDs);

// LANGUAGES
$languageExistsQuery = "SELECT Language_ID FROM book_language WHERE language_name = ?";
$languageExistsStmt = mysqli_prepare($conn, $languageExistsQuery);
mysqli_stmt_bind_param($languageExistsStmt, 's', $language);
mysqli_stmt_execute($languageExistsStmt);
$languageExistsResult = mysqli_stmt_get_result($languageExistsStmt);

if (mysqli_num_rows($languageExistsResult) > 0) {
  $languageExistsRow = mysqli_fetch_assoc($languageExistsResult);
  $existingLanguageID = $languageExistsRow['Language_ID']; // Store existing language's ID
  $Language_ID = $existingLanguageID; // Use existing language's ID
} else {
  // The language is new, insert it into the book_language table
  $insertLanguageQuery = "INSERT INTO book_language (language_name) VALUES (?)";
  $insertLanguageStmt = mysqli_prepare($conn, $insertLanguageQuery);
  mysqli_stmt_bind_param($insertLanguageStmt, 's', $language);
  mysqli_stmt_execute($insertLanguageStmt);

  // Retrieve the ID of the newly inserted language
  $insertedLanguageID = mysqli_insert_id($conn);
  $Language_ID = $insertedLanguageID; // Use newly inserted language's ID
}






// Prepare the insert query FOR BOOKS
  $insertBookQuery = "INSERT INTO book (title, publisher, ISBN, pg_numbers, summary, image_URL, Language_ID)
                      VALUES (?, ?, ?, ?, ?, ?, ?)";
  $insertBookStmt = mysqli_prepare($conn, $insertBookQuery);
  mysqli_stmt_bind_param($insertBookStmt, 'ssssssi', $title, $publisher, $ISBN, $pg_numbers, $summary, $image_URL, $Language_ID);
  if (mysqli_stmt_execute($insertBookStmt)) {
     // Get the ID of the newly inserted book
  $selectBookIDQuery = "SELECT LAST_INSERT_ID() AS Book_ID";
  $selectBookIDResult = mysqli_query($conn, $selectBookIDQuery);
  $bookIDRow = mysqli_fetch_assoc($selectBookIDResult);
  $bookID = $bookIDRow['Book_ID'];

  // Update book_author table
  if (!empty($allAuthorIDs)) {
    $insertBookAuthorQuery = "INSERT INTO book_author (Book_ID, Author_ID) VALUES (?, ?)";
    $insertBookAuthorStmt = mysqli_prepare($conn, $insertBookAuthorQuery);

    foreach ($allAuthorIDs as $authorID) {
      mysqli_stmt_bind_param($insertBookAuthorStmt, 'ii', $bookID, $authorID);
      mysqli_stmt_execute($insertBookAuthorStmt);
    }
  }

 // Update book_category table
if (!empty($allCategoryIDs)) {
  $insertBookCategoryQuery = "INSERT INTO book_category (Book_ID, Category_ID) VALUES (?, ?)";
  $insertBookCategoryStmt = mysqli_prepare($conn, $insertBookCategoryQuery);

  foreach ($allCategoryIDs as $categoryID) {
      mysqli_stmt_bind_param($insertBookCategoryStmt, 'ii', $bookID, $categoryID);
      if (!mysqli_stmt_execute($insertBookCategoryStmt)) {
          // Handle insertion failure
          echo "Failed to add book category: " . mysqli_error($conn);
          exit();
      }
  }
}
  
$keywordList = explode(",", $keywords);
// Update book_keywords table
if (!empty($keywords)) {
  $insertBookKeywordQuery = "INSERT INTO book_keyword (Book_ID, keyword) VALUES (?, ?)";
  $insertBookKeywordStmt = mysqli_prepare($conn, $insertBookKeywordQuery);

  foreach ($keywordList as $keyword) {
    mysqli_stmt_bind_param($insertBookKeywordStmt, 'is', $bookID, $keyword);
    mysqli_stmt_execute($insertBookKeywordStmt);
  }
}
  
  // Prepare the insert query for school_book
  $insertSchoolBookQuery = "INSERT INTO school_book (School_ID, Book_ID, Available_Copies) VALUES (?, ?, ?)";
  $insertSchoolBookStmt = mysqli_prepare($conn, $insertSchoolBookQuery);
  mysqli_stmt_bind_param($insertSchoolBookStmt, 'iii', $schoolID, $bookID, $availablecopies);

  if (mysqli_stmt_execute($insertSchoolBookStmt)) {
    header("Location: school_details.php?School_ID=" . $schoolID);
    // Redirect to the school details page or display a success message
    exit();
  } else {
    // Handle the insertion failure
    echo "Failed to add the book to the school: " . mysqli_error($conn);
    exit();
  }
} else {
  // Handle the insertion failure
  echo "Failed to add the book: " . mysqli_error($conn);
  exit();
}





}











?>

<!DOCTYPE html>
<html>
<head>
  <title>Add a Book</title>
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
  <h2 class="primary-color">Add a Book</h2>
  <form action="add_book.php" method="POST">
    <label for="title">Title:</label>
    <input type="text" id="title" name="title" required><br>

    <label for="publisher">Publisher:</label>
    <input type="text" id="publisher" name="publisher" required><br>

    <label for="ISBN">ISBN:</label>
    <input type="text" id="ISBN" name="ISBN" required><br>
    <label for="ISBN">Available Copies:</label>
    <input type="number" id="availablecopies" name="availablecopies" required><br>
    <label for="pg_numbers">Number of Pages:</label>
    <input type="number" id="pg_numbers" name="pg_numbers" required><br>

    <label for="summary">Summary:</label>
    <textarea id="summary" name="summary" required></textarea><br>

    <label for="image_URL">Image URL:</label>
    <input type="text" id="image_URL" name="image_URL"><br>

    <label for="keyword">Keyword:</label>
    <input type="text" id="keyword" name="keyword"><br>

    <label for="language">Language:</label>
    <input type="text" name="language" required><br>

    <label for="author">Author:</label>
    <div id="authors-container">
      <input type="text" name="author[]" required>
    </div>
    <button type="button" onclick="addAuthorField()">Add Author</button><br>

    <label for="category">Category:</label>
    <div id="category-container">
      <input type="text" name="category[]" required>
    </div>
    <button type="button" onclick="addCategoryField()">Add Category</button><br>
    <input type="submit" value="Add Book">
   


  <!-- Rest of the form elements -->
</form>

</body>
</html>
