<?php
include("connect.php");
session_start();
if (isset($_POST['Author_ID'])  && isset($_POST["Delete_Author"])  ){
    $authorID = $_POST['Author_ID'];

                // Prepare a delete statement
                $sql = "DELETE FROM book_author WHERE Author_ID = ?";

                if ($stmt = $conn->prepare($sql)) {
                    // Bind variables to the prepared statement as parameters
                    $stmt->bind_param("i", $authorID);

                    // Attempt to execute the prepared statement
                    if ($stmt->execute()) {
                        echo "Author was deleted successfully.";
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }
                }
                // Close statement
                $stmt->close();
            }

            if (isset($_POST['Category_ID'])  && isset($_POST["Delete_Category"])  ){
                $authorID = $_POST['Category_ID'];

                // Prepare a delete statement
                $sql = "DELETE FROM book_category WHERE Category_ID = ?";

                if ($stmt = $conn->prepare($sql)) {
                    // Bind variables to the prepared statement as parameters
                    $stmt->bind_param("i", $authorID);

                    // Attempt to execute the prepared statement
                    if ($stmt->execute()) {
                        echo "Category was deleted successfully.";
                    } else {
                        echo "Oops! Something went wrong. Please try again later.";
                    }
                }
                // Close statement
                $stmt->close();
            }

            if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['E_authorID'])) {
                $_SESSION['Author_ID'] = $_POST['E_authorID'];
                $_SESSION['author_fullname'] = $_POST['E_authorname'];

                header("Location: edit_author.php"); // Redirect to make_reservation.php after storing the book ID in the session
                exit;
            }
            
            if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['E_categoryID'])) {
                $_SESSION['Category_ID'] = $_POST['E_categoryID'];
                $_SESSION['category'] = $_POST['E_category'];

                header("Location: edit_category.php"); // Redirect to make_reservation.php after storing the book ID in the session
                exit;
            }
            
            $submittedSuccessfully = false;

            // Check if Book_ID is present in the URL
            if (isset($_SESSION['Book_ID'])) {
                $bookID = $_SESSION['Book_ID'];
                if (isset($_SESSION['School_ID'])) {
                    $schoolID = $_SESSION['School_ID'];
                } else {
                    echo "School_ID not provided";
                    exit;
                }
                $bookDetailsQuery = "SELECT * FROM book WHERE Book_ID = ?";
                $bookDetailsStmt = mysqli_prepare($conn, $bookDetailsQuery);
                mysqli_stmt_bind_param($bookDetailsStmt, 'i', $bookID);
                mysqli_stmt_execute($bookDetailsStmt);
                mysqli_stmt_bind_result($bookDetailsStmt, $Book_ID, $title_old, $publisher_old, $ISBN_old, $pg_numbers_old,$keyword_old ,$summary_old, $image_URL_old, $languageID);
                mysqli_stmt_fetch($bookDetailsStmt);
                mysqli_stmt_close($bookDetailsStmt);
                
                $booklanguageQuery = "SELECT  bl.language_name
                FROM book b
                INNER JOIN book_language bl ON b.Language_ID = bl.Language_ID
                WHERE b.book_ID = ?";
                $booklanguageStmt = mysqli_prepare($conn, $booklanguageQuery);
                mysqli_stmt_bind_param($booklanguageStmt, 'i', $bookID);
                mysqli_stmt_execute($booklanguageStmt);
                mysqli_stmt_bind_result($booklanguageStmt, $languageName_old);
                // Fetch the result
                mysqli_stmt_fetch($booklanguageStmt);
                mysqli_stmt_close($booklanguageStmt);
                // Retrieve available copies
                $copiesQuery = "SELECT available_copies FROM school_book WHERE School_ID = ? AND Book_ID = ?";
                $copiesStmt = mysqli_prepare($conn, $copiesQuery);
                mysqli_stmt_bind_param($copiesStmt, 'ii', $schoolID, $bookID);
                mysqli_stmt_execute($copiesStmt);
                mysqli_stmt_bind_result($copiesStmt, $availableCopies_old);
                mysqli_stmt_fetch($copiesStmt);
                mysqli_stmt_close($copiesStmt);
                
            // Retrieve authors for the book
            $authorsQuery = "SELECT a.Author_ID, a.author_fullname
            FROM author a
            INNER JOIN book_author ba ON a.Author_ID = ba.Author_ID
            WHERE ba.book_ID = ?";
            $authorsStmt = mysqli_prepare($conn, $authorsQuery);
            mysqli_stmt_bind_param($authorsStmt, 'i', $bookID);
            mysqli_stmt_execute($authorsStmt);
            $AuthorResult = mysqli_stmt_get_result($authorsStmt);

            // Free the statement result
            $bookCategoriesQuery = "SELECT  c.category,c.Category_ID
            FROM book_category bc
            INNER JOIN category c ON bc.Category_ID = c.Category_ID
            WHERE bc.Book_ID = ?";
            $bookCategoriesStmt = mysqli_prepare($conn, $bookCategoriesQuery);
            mysqli_stmt_bind_param($bookCategoriesStmt, 'i', $bookID);
            mysqli_stmt_execute($bookCategoriesStmt);
            $CategoryResult= mysqli_stmt_get_result($bookCategoriesStmt);

            // // Retrieve keywords for the book
            // $keywordsQuery = "SELECT  bk.keyword
            //     FROM book_keyword bk
            //     INNER JOIN book b ON bk.Book_ID=b.Book_ID
            //     WHERE b.Book_ID = ?";
            // $keywordsStmt = mysqli_prepare($conn, $keywordsQuery);
            // mysqli_stmt_bind_param($keywordsStmt, 'i', $bookID);
            // mysqli_stmt_execute($keywordsStmt);
            // mysqli_stmt_bind_result($keywordsStmt, $keywordName);

            // Check if the form is submitted
            if ($_SERVER["REQUEST_METHOD"] == "POST") {
                // Handle the form submission
                if (isset($_POST['submit'])) {
                    // Get the values from the form
                    $title = $_POST['title'];
                    $ISBN = $_POST['ISBN'];
                    $summary = $_POST['summary'];
                    $image_URL = $_POST['image_URL'];
                    $pg_numbers = $_POST['pg_numbers'];
                    $language = $_POST['language'];
                    
                    $availableCopies = $_POST['Available_Copies'];
                
                $keyword = $_POST['keyword'];

            
                
                    // Update title
                    $updateTitleQuery = "UPDATE book SET title = ? WHERE Book_ID = ?";
                    $updateTitleStmt = mysqli_prepare($conn, $updateTitleQuery);
                    mysqli_stmt_bind_param($updateTitleStmt, 'si', $title, $bookID);
                    mysqli_stmt_execute($updateTitleStmt);
                    // Update keyword
                    $updateTitleQuery = "UPDATE book SET keyword = ? WHERE Book_ID = ?";
                    $updateTitleStmt = mysqli_prepare($conn, $updateTitleQuery);
                    mysqli_stmt_bind_param($updateTitleStmt, 'si', $keyword, $bookID);
                    mysqli_stmt_execute($updateTitleStmt);


                    // Update title
                    $updateTitleQuery = "UPDATE book SET title = ? WHERE Book_ID = ?";
                    $updateTitleStmt = mysqli_prepare($conn, $updateTitleQuery);
                    mysqli_stmt_bind_param($updateTitleStmt, 'si', $title, $bookID);
                    mysqli_stmt_execute($updateTitleStmt);

                    // Update ISBN
                    $updateISBNQuery = "UPDATE book SET ISBN = ? WHERE Book_ID = ?";
                    $updateISBNStmt = mysqli_prepare($conn, $updateISBNQuery);
                    mysqli_stmt_bind_param($updateISBNStmt, 'si', $ISBN, $bookID);
                    mysqli_stmt_execute($updateISBNStmt);
                    // Update Language
                        $existingLanguageQuery = "SELECT Language_ID FROM book_language WHERE language_name = ?";
                        $existingLanguageStmt = mysqli_prepare($conn, $existingLanguageQuery);
                        mysqli_stmt_bind_param($existingLanguageStmt, 's', $language);
                        mysqli_stmt_execute($existingLanguageStmt);
                        mysqli_stmt_store_result($existingLanguageStmt);

                        if (mysqli_stmt_num_rows($existingLanguageStmt) > 0) {
                            // Language already exists, retrieve the existing ID
                            mysqli_stmt_bind_result($existingLanguageStmt, $languageID);
                            mysqli_stmt_fetch($existingLanguageStmt);
                        } else {
                            // Language is new, insert into book_language table
                            $insertLanguageQuery = "INSERT INTO book_language (language_name) VALUES (?)";
                            $insertLanguageStmt = mysqli_prepare($conn, $insertLanguageQuery);
                            mysqli_stmt_bind_param($insertLanguageStmt, 's', $language);
                            mysqli_stmt_execute($insertLanguageStmt);
                            $languageID = mysqli_insert_id($conn); // Retrieve the last inserted ID
                            mysqli_stmt_close($insertLanguageStmt);
                        }

                        $updateLanguageQuery = "UPDATE book SET Language_ID = ? WHERE Book_ID = ?";
                        $updateLanguageStmt = mysqli_prepare($conn, $updateLanguageQuery);
                        mysqli_stmt_bind_param($updateLanguageStmt, 'ii', $languageID, $bookID); // Use 'ii' for two integers
                        mysqli_stmt_execute($updateLanguageStmt);








                    // Update summary
                    $updateSummaryQuery = "UPDATE book SET summary = ? WHERE Book_ID = ?";
                    $updateSummaryStmt = mysqli_prepare($conn, $updateSummaryQuery);
                    mysqli_stmt_bind_param($updateSummaryStmt, 'si', $summary, $bookID);
                    mysqli_stmt_execute($updateSummaryStmt);

                    // Update image URL
                    $updateImageURLQuery = "UPDATE book SET image_URL = ? WHERE Book_ID = ?";
                    $updateImageURLStmt = mysqli_prepare($conn, $updateImageURLQuery);
                    mysqli_stmt_bind_param($updateImageURLStmt, 'si', $image_URL, $bookID);
                    mysqli_stmt_execute($updateImageURLStmt);

                    // Update page numbers
                    $updatePageNumbersQuery = "UPDATE book SET pg_numbers = ? WHERE Book_ID = ?";
                    $updatePageNumbersStmt = mysqli_prepare($conn, $updatePageNumbersQuery);
                    mysqli_stmt_bind_param($updatePageNumbersStmt, 'si', $pg_numbers, $bookID);
                    mysqli_stmt_execute($updatePageNumbersStmt);

                    // Update the available copies in the school_book table
                    $updateCopiesQuery = "UPDATE school_book SET available_copies = ? WHERE Book_ID = ? AND School_ID = ?";
                    $updateCopiesStmt = mysqli_prepare($conn, $updateCopiesQuery);
                    mysqli_stmt_bind_param($updateCopiesStmt, 'iii', $availableCopies, $bookID, $schoolID);
                    mysqli_stmt_execute($updateCopiesStmt);

                    
                    
                    header("location:edit_book.php ");
                            
}
            }

}
 
    
?>

<!DOCTYPE html>
<html>
<head>
    <title>Update Book Information</title>
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
        table {
            margin-left: auto;
            margin-right: auto;
        }
        .center {
            text-align: center;
        }

        .center button {
            margin: auto;
        }
    </style>
 <script>   

        function deleteAuthor(authorID) {
            var result = confirm("Are you sure you want to delete this author?");
            if (result) {
                event.preventDefault(); // Prevent form submission
                document.getElementById("deleteauthor" + authorID).submit();
            }
        }
        function redirectToAddAuthor() {
                window.location.href = "add_author.php";
            }
        function editAuthor(authorID) {
                event.preventDefault(); // Prevent form submission
                document.getElementById("editauthor" + authorID).submit();
            
        }

        function deleteCategory(categoryID) {
            var result = confirm("Are you sure you want to delete this category?");
            if (result) {
                event.preventDefault(); // Prevent form submission
                document.getElementById("deletecategory" + categoryID).submit();
            }
        }
        function redirectToAddCategory() {
                window.location.href = "add_category.php";
            }
            
        function editCategory(categoryID) {
                event.preventDefault(); // Prevent form submission
                document.getElementById("editcategory" + categoryID).submit();
            
        }  

    

</script>

</head>
<body>
   
    <h1>Update Book Information</h1>
    <form method="POST" action="">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" value="<?php echo $title_old; ?>" required>

        <label for="ISBN">ISBN:</label>
            <input type="text" name="ISBN" id="ISBN" value="<?php echo htmlspecialchars($ISBN_old); ?>" required>
        <label for="summary">Summary:</label>
        <textarea name="summary" id="summary" required><?php echo $summary_old; ?></textarea>
        
        <label for="text">Language:</label>
        <input type="text" name="language" id="language" value="<?php echo  $languageName_old; ?>" required>

        <label for="text">Keywords:</label>
        <input type="text" name="keyword" id="keyword" value="<?php echo  $keyword_old; ?>" required>
  

        <label for="image_URL">Image URL:</label>
        <input type="text" name="image_URL" id="image_URL" value="<?php echo $image_URL_old; ?>" required>

        <label for="pg_numbers">Page Numbers:</label>
        <input type="number" name="pg_numbers" id="pg_numbers" value="<?php echo $pg_numbers_old; ?>" required>

        <label for="number">Available copies:</label>
        <input type="number" name="Available_Copies" id="Available_Copies" value="<?php echo  $availableCopies_old; ?>" required>
           
    <input type="submit" name="submit" value="Update">
</form>
<table class="center">
    <thead>
        <tr>
            <th>Author ID</th>
            <th>Author Name</th>
            
        </tr>
    </thead>
    <tbody>
    <?php while ($row = mysqli_fetch_assoc($AuthorResult)): ?>
        <tr>
            <td><?php echo $row['Author_ID']; ?></td>
            <td><?php echo $row['author_fullname']; ?></td>
            <td>
                <button class="delete-button" onclick="deleteAuthor(<?php echo $row['Author_ID']; ?>)">Delete</button>
                <form id="deleteauthor<?php echo $row['Author_ID']; ?>" method="post" style="display: none;">
                    <input type="hidden" name="Author_ID" value="<?php echo $row['Author_ID']; ?>">
                    <input type="hidden" name="Delete_Author" value=1>

                </form>
                <button class="edit-button" onclick="editAuthor(<?php echo $row['Author_ID']; ?>)">Edit</button>
                <form id="editauthor<?php echo $row['Author_ID']; ?>" method="post" style="display: none;">
                <input type='hidden' name="E_authorID" value="<?php echo $row['Author_ID']; ?>">
                <input type='hidden' name="E_authorname" value="<?php echo $row['author_fullname']; ?>">

                </form>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<div class="center">
<button type="button" onclick="redirectToAddAuthor()">Add Author</button>
</div>


<table class="center">
    <thead>
        <tr>
            <th>Category ID</th>
            <th>Category Name</th>
            
        </tr>
    </thead>
    <tbody>
    <?php while ($row = mysqli_fetch_assoc($CategoryResult)): ?>
        <tr>
            <td><?php echo $row['Category_ID']; ?></td>
            <td><?php echo $row['category']; ?></td>
            <td>
                <button class="delete-button" onclick="deleteCategory(<?php echo $row['Category_ID']; ?>)">Delete</button>
                <form id="deletecategory<?php echo $row['Category_ID']; ?>" method="post" style="display: none;">
                    <input type="hidden" name="Category_ID" value="<?php echo $row['Category_ID']; ?>">
                    <input type="hidden" name="Delete_Category" value=1>
                </form>
                <button class="edit-button" onclick="editCategory(<?php echo $row['Category_ID']; ?>)">Edit</button>
                <form id="editcategory<?php echo $row['Category_ID']; ?>" method="post" style="display: none;">
                <input type='hidden' name="E_categoryID" value="<?php echo $row['Category_ID']; ?>">
                <input type='hidden' name="E_category" value="<?php echo $row['category']; ?>">
        

        </form>
            </td>
        </tr>
    <?php endwhile; ?>
    </tbody>
</table>
<div class="center">
<button type="button" onclick="redirectToAddCategory()">Add Category</button>
</div>

<form action="school_details.php" method="get">
<input type="submit" value="Back">
</form>

</body>
</html>
