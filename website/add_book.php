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
  $category = $_POST['category']; 
  $availablecopies = $_POST['availablecopies']; 


  if (isset($_SESSION['School_ID'])) {
    $schoolID = $_SESSION['School_ID'];
  } else {
    echo "School_ID not provided";
    exit;
  }

 
  
//Authors
$author = $_POST['author'];
 // Check if the author already exists
 $existingAuthorQuery = "SELECT Author_ID FROM author WHERE author_fullname = ?";
 $existingAuthorStmt = mysqli_prepare($conn, $existingAuthorQuery);
 mysqli_stmt_bind_param($existingAuthorStmt, 's', $author);
 mysqli_stmt_execute($existingAuthorStmt);
 mysqli_stmt_store_result($existingAuthorStmt);
 
 if (mysqli_stmt_num_rows($existingAuthorStmt) > 0) {
     // Author already exists, retrieve the existing ID
     mysqli_stmt_bind_result($existingAuthorStmt, $authorID);
     mysqli_stmt_fetch($existingAuthorStmt);
 } else {
     // Author is new, insert into author table
     $insertAuthorQuery = "INSERT INTO author (author_fullname) VALUES (?)";
     $insertAuthorStmt = mysqli_prepare($conn, $insertAuthorQuery);
     mysqli_stmt_bind_param($insertAuthorStmt, 's', $author);
     mysqli_stmt_execute($insertAuthorStmt);
     $authorID = mysqli_stmt_insert_id($insertAuthorStmt);
     mysqli_stmt_close($insertAuthorStmt);
 }
 mysqli_stmt_close($existingAuthorStmt);
 



// categories
 // Check if the Category already exists
 $existingCategoryQuery = "SELECT Category_ID FROM category WHERE category = ?";
 $existingCategoryStmt = mysqli_prepare($conn, $existingCategoryQuery);
 mysqli_stmt_bind_param($existingCategoryStmt, 's', $category);
 mysqli_stmt_execute($existingCategoryStmt);
 mysqli_stmt_store_result($existingCategoryStmt);
 
 if (mysqli_stmt_num_rows($existingCategoryStmt) > 0) {
     // Category already exists, retrieve the existing ID
     mysqli_stmt_bind_result($existingCategoryStmt, $categoryID);
     mysqli_stmt_fetch($existingCategoryStmt);
 } else {
     // Category is new, insert into category table
     $insertCategoryQuery = "INSERT INTO category (category) VALUES (?)";
     $insertCategoryStmt = mysqli_prepare($conn, $insertCategoryQuery);
     mysqli_stmt_bind_param($insertCategoryStmt, 's', $category);
     mysqli_stmt_execute($insertCategoryStmt);
     $categoryID = mysqli_stmt_insert_id($insertCategoryStmt);
     mysqli_stmt_close($insertCategoryStmt);
 }
 mysqli_stmt_close($existingCategoryStmt);



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
$insertBookQuery = "INSERT INTO book (title, publisher, ISBN, pg_numbers, keyword, summary, image_URL, Language_ID)
VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
$insertBookStmt = mysqli_prepare($conn, $insertBookQuery);
mysqli_stmt_bind_param($insertBookStmt, 'sssssssi', $title, $publisher, $ISBN, $pg_numbers, $keywords, $summary, $image_URL, $Language_ID);

  if (mysqli_stmt_execute($insertBookStmt)) {
     // Get the ID of the newly inserted book
  $selectBookIDQuery = "SELECT LAST_INSERT_ID() AS Book_ID";
  $selectBookIDResult = mysqli_query($conn, $selectBookIDQuery);
  $bookIDRow = mysqli_fetch_assoc($selectBookIDResult);
  $bookID = $bookIDRow['Book_ID'];

// Insert into book_author table
$insertBookAuthorQuery = "INSERT INTO book_author (Book_ID, Author_ID) VALUES (?, ?)";
$insertBookAuthorStmt = mysqli_prepare($conn, $insertBookAuthorQuery);
mysqli_stmt_bind_param($insertBookAuthorStmt, 'ii', $bookID, $authorID);
mysqli_stmt_execute($insertBookAuthorStmt);
mysqli_stmt_close($insertBookAuthorStmt);

  // Language is new, insert into book_language table
  $insertLanguageQuery = "INSERT INTO book_language (language_name) VALUES (?)";
  $insertLanguageStmt = mysqli_prepare($conn, $insertLanguageQuery);
  mysqli_stmt_bind_param($insertLanguageStmt, 's', $language);
  mysqli_stmt_execute($insertLanguageStmt);
  $languageID = mysqli_insert_id($conn); // Retrieve the last inserted ID
  mysqli_stmt_close($insertLanguageStmt);


     // Insert into book_author table
     $insertBookCategoryQuery = "INSERT INTO book_category (Book_ID, Category_ID) VALUES (?, ?)";
     $insertBookCategoryStmt = mysqli_prepare($conn, $insertBookCategoryQuery);
     mysqli_stmt_bind_param($insertBookCategoryStmt, 'ii', $bookID, $categoryID);
     mysqli_stmt_execute($insertBookCategoryStmt);
     mysqli_stmt_close($insertBookCategoryStmt);


  

  
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

$query = "SELECT author_fullname FROM Author";
$result1 = mysqli_query($conn, $query);

$Authors1 = array();
while ($row = mysqli_fetch_assoc($result1)) {
    $Authors1[] = $row['author_fullname'];
}





$query = "SELECT category FROM Category";
$result = mysqli_query($conn, $query);

$categories1 = array();
while ($row = mysqli_fetch_assoc($result)) {
    $categories1[] = $row['category'];
}



?>




<!DOCTYPE html>
<html>
<head>
  <title>Add a Book</title>

  <script>
     function redirectToAddAuthor() {
                window.location.href = "add_author.php";
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
<h1 class="primary-color">Add a Book</h1>
<h2 class="primary-color">If you want to add more than one Author or Category for a specific Book, please add them in the edit section.</h2>
 
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
    <input type="text" name="author" list="author_list" placeholder="Enter Author">
    <datalist id="author_list">
      <?php foreach ($Authors1 as $author) { ?>
        <option value="<?php echo $author; ?>">
      <?php } ?>
    </datalist>

        <label for="category">Category:</label>
    <input type="text" name="category" list="category_list" placeholder="Enter a category">
    <datalist id="category_list">
      <?php foreach ($categories1 as $category) { ?>
        <option value="<?php echo $category; ?>">
      <?php } ?>
    </datalist>


    <input type="submit"  method="POST" value="Add Book">

   
</form>



<form action="school_details.php" method="get">
<input type="submit" value="Back to overview Books">
</form>

</body>
</html>
