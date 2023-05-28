<?php
    include("connect.php");
    session_start();

    // Check if Book_ID is present in the URL
    if (isset($_GET['Book_ID'])) {
        $bookID = $_GET['Book_ID'];
        if (isset($_SESSION['School_ID'])) {
            $schoolID = $_SESSION['School_ID'];
        } else {
            echo "School_ID not provided";
            exit;
        }
        // Check if the form is submitted
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            // Handle the form submission
            if (isset($_POST['submit'])) {
                // Get the values from the form
                $title = $_POST['title'];
                $ISBN = $_POST['ISBN'];
                $summary = $_POST['summary'];
                $language = $_POST['language'];
                $image_URL = $_POST['image_URL'];
                $pg_numbers = $_POST['pg_numbers'];
                
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

                // Redirect to a success page or display a success message
                header("Location: success.php");
                exit;
            }
        }
    }
?>

<!DOCTYPE html>
<html>
<head>
    <title>Edit Book</title>
    <style>
        table {
            border-collapse: collapse;
        }
        th, td {
            padding: 5px;
            border: 1px solid black;
        }
    </style>
</head>
<body>
    <h1>Edit Book</h1>
    <?php

// Fetch existing book details from the database
$fetchBookQuery = "SELECT title, ISBN, summary, image_URL FROM book WHERE Book_ID = ?";
$fetchBookStmt = mysqli_prepare($conn, $fetchBookQuery);
mysqli_stmt_bind_param($fetchBookStmt, 'i', $bookID);
mysqli_stmt_execute($fetchBookStmt);
$bookResult = mysqli_stmt_get_result($fetchBookStmt);

// Check if the book exists
if (mysqli_num_rows($bookResult) > 0) {
    $book = mysqli_fetch_assoc($bookResult);
    $existingTitle = $book['title'];
    $existingISBN = $book['ISBN'];
    $existingSummary = $book['summary'];
    $existingImageURL = $book['image_URL'];
}
?>

    <div style="border: 1px solid black; padding: 10px;">
        <h2>Existing Details</h2>
        <p><strong>Title:</strong> <?php echo $existingTitle; ?></p>
        <p><strong>ISBN:</strong> <?php echo $existingISBN; ?></p>
        <p><strong>Summary:</strong> <?php echo $existingSummary; ?></p>
        <p><strong>Image URL:</strong> <?php echo $existingImageURL; ?></p>
    </div>

    <form method="POST" action="edit_book.php?Book_ID=<?php echo $bookID; ?>">
        
        <!-- Authors -->
        <label for="authors">Authors:</label>
        <table>
            <thead>
                <tr>
                    <th>Author</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    // Fetch existing authors from the database
                    $fetchAuthorsQuery = "SELECT author_name FROM authors WHERE Book_ID = ?";
                    $fetchAuthorsStmt = mysqli_prepare($conn, $fetchAuthorsQuery);
                    mysqli_stmt_bind_param($fetchAuthorsStmt, 'i', $bookID);
                    mysqli_stmt_execute($fetchAuthorsStmt);
                    $authorsResult = mysqli_stmt_get_result($fetchAuthorsStmt);

                    while ($author = mysqli_fetch_assoc($authorsResult)) {
                        echo "<tr>";
                        echo "<td>" . $author['author_name'] . "</td>";
                        echo "<td><button type='submit' name='delete_author'>Delete</button></td>";
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
        <input type="text" name="new_author">
        <button type="submit" name="add_author">Add</button>
        <br><br>

        <!-- Keywords -->
        <label for="keywords">Keywords:</label>
        <table>
            <thead>
                <tr>
                    <th>Keyword</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    // Fetch existing keywords from the database
                    $fetchKeywordsQuery = "SELECT keyword FROM keywords WHERE Book_ID = ?";
                    $fetchKeywordsStmt = mysqli_prepare($conn, $fetchKeywordsQuery);
                    mysqli_stmt_bind_param($fetchKeywordsStmt, 'i', $bookID);
                    mysqli_stmt_execute($fetchKeywordsStmt);
                    $keywordsResult = mysqli_stmt_get_result($fetchKeywordsStmt);

                    while ($keyword = mysqli_fetch_assoc($keywordsResult)) {
                        echo "<tr>";
                        echo "<td>" . $keyword['keyword'] . "</td>";
                        echo "<td><button type='submit' name='delete_keyword'>Delete</button></td>";
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
        <input type="text" name="new_keyword">
        <button type="submit" name="add_keyword">Add</button>
        <br><br>

        <!-- Categories -->
        <label for="categories">Categories:</label>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Action</th>
                </tr>
            </thead>
            <tbody>
                <?php
                    // Fetch existing categories from the database
                    $fetchCategoriesQuery = "SELECT category FROM book_category WHERE Book_ID = ?";
                    $fetchCategoriesStmt = mysqli_prepare($conn, $fetchCategoriesQuery);
                    mysqli_stmt_bind_param($fetchCategoriesStmt, 'i', $bookID);
                    mysqli_stmt_execute($fetchCategoriesStmt);
                    $categoriesResult = mysqli_stmt_get_result($fetchCategoriesStmt);

                    while ($category = mysqli_fetch_assoc($categoriesResult)) {
                        echo "<tr>";
                        echo "<td>" . $category['category'] . "</td>";
                        echo "<td><button type='submit' name='delete_category'>Delete</button></td>";
                        echo "</tr>";
                    }
                ?>
            </tbody>
        </table>
        <input type="text" name="new_category">
        <button type="submit" name="add_category">Add</button>
        <br><br>

        <input type="submit" name="submit" value="Save Changes">
    </form>
</body>
</html>
