
<?php
session_start();
include("connect.php");


$query = " SELECT 
    L1.Manager_ID, 
    COUNT(*) AS Num_Loans
FROM 
    Loan L1
WHERE 
    L1.loan_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 YEAR) AND CURDATE()
GROUP BY 
    L1.Manager_ID
HAVING 
    COUNT(*) > 20
AND 
    COUNT(*) IN (
        SELECT 
            COUNT(*) AS Num_Loans
        FROM 
            Loan L2
        WHERE 
            L2.loan_date BETWEEN DATE_SUB(CURDATE(), INTERVAL 1 YEAR) AND CURDATE()
        GROUP BY 
            L2.Manager_ID
    )";


$result = mysqli_query($conn, $query);
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Managers that have more than 20 loans of in one year</title>
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
<h1>Managers that have more than 20 loans of in one year </h1>    <?php
    if (mysqli_num_rows($result) > 0) {
        echo "<table>";
        echo "<tr>
                <th>Manager ID</th>
                <th>First Name</th
                <th>Last Name</th
                <th>No of books borrowed</th
              </tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['Manager_ID'] . "</td>";
            echo "<td>" . $row['first_name'] . "</td>";
            echo "<td>" . $row['last_name'] . "</td>";
            echo "<td>" . $row['number_of_books_borrowed'] . "</td>";

            
            echo "</tr>";
        }
        echo "</table>";
    } else {
        echo "No results found.";
    }
    ?>
</body>
</html>

<?php
// Don't forget to close the database connection
mysqli_close($conn);
?>
