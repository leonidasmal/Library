
<?php
session_start();
include("connect.php");


$query = "
    SELECT author_fullname,
           COUNT(*) AS num_books,
           CASE
               WHEN COUNT(*) >= (SELECT COUNT(*) FROM Book_Author GROUP BY Author_ID ORDER BY COUNT(*) DESC LIMIT 1) THEN 'A'
               WHEN COUNT(*) >= (SELECT COUNT(*) FROM Book_Author GROUP BY Author_ID ORDER BY COUNT(*) DESC LIMIT 1) - 5 THEN 'B'
               ELSE 'C'
           END AS ABC_group
    FROM Book_Author
    JOIN Author ON Book_Author.Author_ID = Author.Author_ID
    GROUP BY author_fullname;
";

$result = mysqli_query($conn, $query); // assuming $connection is your database connection variable

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
<h1>Authors report </h1>   

    <table>
        <tr>
            <th>Author Full Name</th>
            <th>Number of Books</th>
            <th>ABC Group</th>
        </tr>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['author_fullname'] . "</td>";
            echo "<td>" . $row['num_books'] . "</td>";
            echo "<td>" . $row['ABC_group'] . "</td>";
            echo "</tr>";
        }
        ?>
    </table>
</body>
</html>