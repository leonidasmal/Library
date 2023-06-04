<?php
session_start();
include("connect.php");

$query = "
    SELECT A.Author_ID, A.author_fullname, COUNT(*) AS book_count
    FROM Author A
    JOIN Book_Author BA ON A.Author_ID = BA.Author_ID
    GROUP BY A.Author_ID
    HAVING book_count <= (
        SELECT COUNT(*)
        FROM Book_Author
        GROUP BY Author_ID
        ORDER BY COUNT(*) DESC
        LIMIT 1
    ) - 5;
";

$result = mysqli_query($conn, $query); // assuming $connection is your database connection variable

if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

?>

<!DOCTYPE html>
<html>
<head>
    <title>Authors Report</title>
    <style>
        table {
            border-collapse: collapse;
            margin: 0 auto; /* Add this line to center the table */
            width: 50%;
        }
        h1 {
            text-align: center;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center; /* Add this line */
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Authors Report</h1>   
    <table>
        <tr>
            <th>Author ID</th>
            <th>Author Name</th>
            <th>Book Count</th>
        </tr>
        <?php
        if (mysqli_num_rows($result) > 0) {
            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['Author_ID'] . "</td>";
                echo "<td>" . $row['author_fullname'] . "</td>";
                echo "<td>" . $row['book_count'] . "</td>";
                echo "</tr>";
            }
        } else {
            echo "<tr><td colspan='3'>No authors found.</td></tr>";
        }
        ?>
    </table>

    <form action="admin_dashboard.php" method="get">
<input type="submit" value="Back">
</form>
</body>
</html>

<?php
// Don't forget to close the database connection
mysqli_close($conn);
?>
