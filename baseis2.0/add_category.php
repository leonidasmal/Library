<?php
session_start();
include("connect.php");

// Check if Book_ID is present in the URL
if (isset($_SESSION['Book_ID'])) {
    $bookID = $_SESSION['Book_ID'];
} else {
    echo "Book_ID not provided";
    exit;
}

if (isset($_SESSION['School_ID'])) {
    $schoolID = $_SESSION['School_ID'];
} else {
    echo "School_ID not provided";
    exit;
}
$_SESSION['Book_ID'] = $bookID;
$_SESSION['School_ID'] = $schoolID;
// Retrieve book title
$titleQuery = "SELECT title FROM book WHERE Book_ID = ?";
$titleStmt = mysqli_prepare($conn, $titleQuery);
mysqli_stmt_bind_param($titleStmt, 'i', $bookID);
mysqli_stmt_execute($titleStmt);
mysqli_stmt_bind_result($titleStmt, $bookTitle);

if (mysqli_stmt_fetch($titleStmt)) {
    $bookName = $bookTitle;
} else {
    echo "Book not found";
    exit;
}

mysqli_stmt_close($titleStmt);
// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Handle the form submission
    $baut=true; 



    if (isset($_POST['submit'])) {
        // Retrieve author name from the form
        $category = $_POST['category'];
     
       // Check if the author already exists
       $existingCategoryQuery = "SELECT Category_ID FROM category WHERE category = ?";
       $existingCategoryStmt = mysqli_prepare($conn, $existingCategoryQuery);
       mysqli_stmt_bind_param($existingCategoryStmt, 's', $category);
       mysqli_stmt_execute($existingCategoryStmt);
       mysqli_stmt_store_result($existingCategoryStmt);
       
       if (mysqli_stmt_num_rows($existingCategoryStmt) > 0) {
           // Author already exists, retrieve the existing ID
           mysqli_stmt_bind_result($existingCategoryStmt, $categoryID);
           mysqli_stmt_fetch($existingCategoryStmt);
       } else {
           // Author is new, insert into author table
           $insertCategoryQuery = "INSERT INTO category (category) VALUES (?)";
           $insertCategoryStmt = mysqli_prepare($conn, $insertCategoryQuery);
           mysqli_stmt_bind_param($insertCategoryStmt, 's', $category);
           mysqli_stmt_execute($insertCategoryStmt);
           $categoryID = mysqli_stmt_insert_id($insertCategoryStmt);
           mysqli_stmt_close($insertCategoryStmt);
       }
       mysqli_stmt_close($existingCategoryStmt);
       // Check if the book_author already exists
       $existingBookCategoryQuery = "SELECT Book_ID FROM book_category  WHERE Book_ID = ? AND Category_ID = ?";
       $existingBookCategoryStmt = mysqli_prepare($conn, $existingBookCategoryQuery);
       mysqli_stmt_bind_param($existingBookCategoryStmt, 'ii', $bookID, $categoryID);
       mysqli_stmt_execute($existingBookCategoryStmt);
       mysqli_stmt_store_result($existingBookCategoryStmt);


       if (mysqli_stmt_num_rows($existingBookCategoryStmt) > 0) {
            $baut=false; 
           echo 'This category is already associated with this book.';

       } else {
           // Insert into book_author table
           $insertBookCategoryQuery = "INSERT INTO book_category (Book_ID, Category_ID) VALUES (?, ?)";
           $insertBookCategoryStmt = mysqli_prepare($conn, $insertBookCategoryQuery);
           mysqli_stmt_bind_param($insertBookCategoryStmt, 'ii', $bookID, $categoryID);
           mysqli_stmt_execute($insertBookCategoryStmt);
           mysqli_stmt_close($insertBookCategoryStmt);
       }
       mysqli_stmt_close($existingBookCategoryStmt);
   

       if ($baut){
        // Redirect to edit_book.php
        header("Location: edit_book.php");
       }
    }
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Add Author</title>
    <style>
        body {
            font-family: Arial, sans-serif;
        }
        form {
            width: 300px;
            margin: 0 auto;
        }
        label {
            display: block;
            margin-top: 20px;
        }
        input, textarea {
            width: 100%;
            padding: 10px;
            margin-top: 5px;
        }
        input[type="submit"] {
            margin-top: 20px;
            background-color: #4CAF50;
            color: white;
            cursor: pointer;
        }
        input[type="submit"]:hover {
            background-color: #45a049;
        }
        .success-message {
            color: green;
            font-weight: bold;
            font-size: 18px;
            text-align: center;
            margin-top: 20px;
        }
        .emoji {
            font-size: 40px;
            margin-bottom: 10px;
        }
    </style>
</head>
<body>
    <h1>You are adding a new category in the book <?php echo $bookName; ?></h1>
    <form method="POST" action="">
        <label for="category">Provide the category of the book:</label>
        <input type="text" name="category" id="category" required>

        <input type="submit" name="submit" value="Add Category">
    </form>
</body>
</html>
