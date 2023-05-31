<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Authors and Teachers by Category</title>
</head>
<body>
    <h1>Authors and Teachers by Category</h1>
    <form action="3.1.2.php" method="GET">
        <label for="Category_ID">Select a Category:</label>
        <select name="Category_ID" id="Category_ID">
            <?php
            include("connect.php");

            // Retrieve the categories from the database
            $categoryQuery = "SELECT Category_ID, category FROM Category";
            $categoryResult = mysqli_query($conn, $categoryQuery);

            // Display the categories in the dropdown select options
            while ($categoryRow = mysqli_fetch_assoc($categoryResult)) {
                echo "<option value='" . $categoryRow['Category_ID'] . "'>" . $categoryRow['category'] . "</option>";
            }

            // Don't forget to close the database connection
            mysqli_close($conn);
            ?>
        </select>
        <br>
        <button type="submit">Retrieve Authors and Teachers</button>
    </form>

    <?php
    if (isset($_GET['Category_ID'])) {
        include("connect.php");

        $categoryID = $_GET['Category_ID'];

        // Retrieve authors belonging to the specific category
        $authorQuery = "SELECT DISTINCT A.author_fullname
                        FROM Author A
                        JOIN Book_Author BA ON A.Author_ID = BA.Author_ID
                        JOIN Book B ON BA.Book_ID = B.Book_ID
                        JOIN Book_Category BC ON B.Book_ID = BC.Book_ID
                        JOIN Category C ON BC.Category_ID = C.Category_ID
                        WHERE C.category_id = ?;";
        $authorStmt = mysqli_prepare($conn, $authorQuery);
        mysqli_stmt_bind_param($authorStmt, 'i', $categoryID);
        mysqli_stmt_execute($authorStmt);
        $authorResult = mysqli_stmt_get_result($authorStmt);

        // Retrieve teachers who borrowed books from the category in the past year
        $teacherQuery = "SELECT DISTINCT U.first_name, U.last_name
                        FROM Students_Professors SP
                        JOIN Users U ON SP.User_ID = U.User_ID
                        JOIN Loan L ON SP.studprof_id = L.studprof_id
                        JOIN Book B ON L.Book_ID = B.Book_ID
                        JOIN Book_Category BC ON B.Book_ID = BC.Book_ID
                        JOIN Category C ON BC.Category_ID = C.Category_ID
                        WHERE C.category_id = ? AND SP.Is_Professor = 1 AND L.loan_date >= DATE_SUB(CURDATE(), INTERVAL 1 YEAR)";
        $teacherStmt = mysqli_prepare($conn, $teacherQuery);
        mysqli_stmt_bind_param($teacherStmt, 'i', $categoryID);
        mysqli_stmt_execute($teacherStmt);
        $teacherResult = mysqli_stmt_get_result($teacherStmt);

        // Display the list of authors and teachers
        echo "<h4>Authors belonging to the category:</h4>";
        echo "<ul>";
        while ($authorRow = mysqli_fetch_assoc($authorResult)) {
            echo "<li>" . $authorRow['author_fullname'] . "</li>";
        }
        echo "</ul>";

        echo "<h4>Teachers who borrowed books from the category in the past year:</h4>";
        echo "<ul>";
        while ($teacherRow = mysqli_fetch_assoc($teacherResult)) {
            echo "<li>" . $teacherRow['first_name'] . " " . $teacherRow['last_name'] . "</li>";
        }
        echo "</ul>";

        // Don't forget to close the statements and connection when you're done
        mysqli_stmt_close($authorStmt);
        mysqli_stmt_close($teacherStmt);
        mysqli_close($conn);
    }
    ?>
</body>
</html>
