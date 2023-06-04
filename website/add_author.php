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
        $author = $_POST['author_name'];
     
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
       // Check if the book_author already exists
       $existingBookAuthorQuery = "SELECT Book_ID FROM book_author WHERE Book_ID = ? AND Author_ID = ?";
       $existingBookAuthorStmt = mysqli_prepare($conn, $existingBookAuthorQuery);
       mysqli_stmt_bind_param($existingBookAuthorStmt, 'ii', $bookID, $authorID);
       mysqli_stmt_execute($existingBookAuthorStmt);
       mysqli_stmt_store_result($existingBookAuthorStmt);


       if (mysqli_stmt_num_rows($existingBookAuthorStmt) > 0) {
            $baut=false; 
           echo 'This author is already associated with this book.';

       } else {
           // Insert into book_author table
           $insertBookAuthorQuery = "INSERT INTO book_author (Book_ID, Author_ID) VALUES (?, ?)";
           $insertBookAuthorStmt = mysqli_prepare($conn, $insertBookAuthorQuery);
           mysqli_stmt_bind_param($insertBookAuthorStmt, 'ii', $bookID, $authorID);
           mysqli_stmt_execute($insertBookAuthorStmt);
           mysqli_stmt_close($insertBookAuthorStmt);
       }
       mysqli_stmt_close($existingBookAuthorStmt);
   

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
    <h1>You are adding a new author in the book <?php echo $bookName; ?></h1>
    <form method="POST" action="">
        <label for="author_name">Provide the Author's Full name:</label>
        <input type="text" name="author_name" id="author_name" required>

        <input type="submit" name="submit" value="Add Author">
    </form>
</body>
</html>
