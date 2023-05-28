<?php
include("connect.php");
session_start();

$submittedSuccessfully = false;

// Check if Book_ID is present in the URL
if (isset($_GET['Book_ID'])) {
    $bookID = $_GET['Book_ID'];
    if (isset($_SESSION['School_ID'])) {
        $schoolID = $_SESSION['School_ID'];
    } else {
        echo "School_ID not provided";
        exit;
    }

    $BookDeatailsQuery = "SELECT * FROM book WHERE Book_ID = ?";
    $bookDetailsStmt = mysqli_prepare($conn, $BookDeatailsQuery);
    mysqli_stmt_bind_param($bookDetailsStmt, 'i', $bookID);
    mysqli_stmt_execute($bookDetailsStmt);
    mysqli_stmt_bind_result($bookDetailsStmt, $Book_ID, $title_old, $publisher_old, $ISBN_old, $pg_numbers_old, $summary_old, $image_URL_old, $language_old);
    mysqli_stmt_fetch($bookDetailsStmt);
    mysqli_stmt_close($bookDetailsStmt);

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

            // Update page numbers
            $updatePageNumbersQuery = "UPDATE book SET pg_numbers = ? WHERE Book_ID = ?";
            $updatePageNumbersStmt = mysqli_prepare($conn, $updatePageNumbersQuery);
            mysqli_stmt_bind_param($updatePageNumbersStmt, 'si', $pg_numbers, $bookID);
            mysqli_stmt_execute($updatePageNumbersStmt);

            // Display success message
            echo '<div class="success-message">';
            echo '<div class="emoji">✨</div>';
            echo 'Update Successful!';
            echo '<div class="emoji">✨</div>';
            echo '</div>';

            $submittedSuccessfully = true;
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
    </style>
</head>
<body>
    <?php if (!$submittedSuccessfully): ?>
    <h1>Update Book Information</h1>
    <form method="POST" action="">
        <label for="title">Title:</label>
        <input type="text" name="title" id="title" value="<?php echo $title_old; ?>" required>

        <label for="ISBN">ISBN:</label>
        <input type="text" name="ISBN" id="ISBN" value="<?php echo $ISBN_old; ?>" required>

        <label for="summary">Summary:</label>
        <textarea name="summary" id="summary" required><?php echo $summary_old; ?></textarea>

        <label for="image_URL">Image URL:</label>
        <input type="text" name="image_URL" id="image_URL" value="<?php echo $image_URL_old; ?>" required>

        <label for="pg_numbers">Page Numbers:</label>
        <input type="number" name="pg_numbers" id="pg_numbers" value="<?php echo $pg_numbers_old; ?>" required>

        <input type="submit" name="submit" value="Update">
    </form>
    <?php endif; ?>
</body>
</html>
