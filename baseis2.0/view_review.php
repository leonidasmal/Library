<?php
include("connect.php");
session_start();
// Retrieve the Book_ID from the GET parameter
$bookID = $_SESSION['Book_ID'];

// Prepare the SQL query
$reviewsQuery = "
    SELECT
        r.likert_scale,
        r.review,
        u.username
    FROM
        Review r
    INNER JOIN
        users u ON r.User_ID = u.User_ID
    WHERE
        r.Book_id = $bookID
        AND r.approved = 1";

// Execute the query
$reviewsResult = mysqli_query($conn, $reviewsQuery);

if (!$reviewsResult) {
    echo "Query execution failed: " . mysqli_error($conn);
    exit;
}

// Check if there are any approved reviews
if (mysqli_num_rows($reviewsResult) > 0) {
    // Display the approved reviews
    while ($row = mysqli_fetch_assoc($reviewsResult)) {
        $likertScale = $row['likert_scale'];
        $review = $row['review'];
        $username = $row['username'];

        // Map the likert scale value to the corresponding label
        $likertLabel = "";
        switch ($likertScale) {
            case 1:
                $likertLabel = "1 - Not at All";
                break;
            case 2:
                $likertLabel = "2 - Not Really";
                break;
            case 3:
                $likertLabel = "3 - Neutral";
                break;
            case 4:
                $likertLabel = "4 - Somewhat";
                break;
            case 5:
                $likertLabel = "5 - Very Much";
                break;
        }

        // Display the review information
        echo "<p><strong>How much I liked it:</strong> $likertLabel</p>";
        echo "<p><strong>Review:</strong> $review</p>";
        echo "<p><strong>Username:</strong> $username</p>";
        echo "<hr>";
    }
} else {
    echo "No approved reviews found for this book.";
}

// Free the result set
mysqli_free_result($reviewsResult);

// Close the database connection
mysqli_close($conn);
?>
<form action="user_view_books.php" method="get">
    <input type="submit" value="Back">
</form>