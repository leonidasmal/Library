<?php
session_start();
include("connect.php");

// Retrieve data from the database
$query = "
    SELECT 
        C1.category AS Category1, 
        C2.category AS Category2, 
        COUNT(*) AS PairCount
    FROM 
        Loan L
    JOIN 
        Book_Category BC1 ON L.Book_ID = BC1.Book_ID
    JOIN 
        Book_Category BC2 ON L.Book_ID = BC2.Book_ID
    JOIN
        Category C1 ON BC1.Category_ID = C1.Category_ID
    JOIN
        Category C2 ON BC2.Category_ID = C2.Category_ID
    WHERE 
        BC1.Category_ID < BC2.Category_ID
    GROUP BY 
        BC1.Category_ID, 
        BC2.Category_ID
    ORDER BY 
        PairCount DESC
    LIMIT 3;
";

$result = mysqli_query($conn, $query); // assuming $connection is your database connection variable

?>

<!DOCTYPE html>
<html>
<head>
    <title>Top Book Category Pairs</title>
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
    <h1>Top Category Pairs in Books</h1>

    <table>
        <tr>
            <th>Category 1</th>
            <th>Category 2</th>
            <th>Pair Count</th>
        </tr>
        <?php
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['Category1'] . "</td>";
            echo "<td>" . $row['Category2'] . "</td>";
            echo "<td>" . $row['PairCount'] . "</td>";
            echo "</tr>";
        }
        ?>
    </table>
    <form action="admin_dashboard.php" method="get">
<input type="submit" value="Back">
</form>


</body>
</html>
