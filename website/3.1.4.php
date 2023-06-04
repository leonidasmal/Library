<?php
session_start();
include("connect.php");


$query = "SELECT A.author_fullname, A.Author_ID
FROM Author A
WHERE A.Author_ID NOT IN (
    SELECT BA.Author_ID
    FROM Loan L
    JOIN Book_Author BA ON L.Book_ID = BA.Book_ID)";

$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professors  with Most Books Borrowed</title>
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
<h1>Authors whose books have not been borrowed. </h1>    <?php
    if (mysqli_num_rows($result) > 0) {
        echo "<table>";
        echo "<tr>
                <th>Author ID</th>
                <th>Fullname of Author</th
              </tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['Author_ID'] . "</td>";
            echo "<td>" . $row['author_fullname'] . "</td>";
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No results found.";
    }
    ?>
        <form action="admin_dashboard.php" method="get">
<input type="submit" value="Back">
</form>
</body>
</html>

<?php
// Don't forget to close the database connection
mysqli_close($conn);
?>
