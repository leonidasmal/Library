<?php
session_start();
include("connect.php");

$query = "SELECT *
    FROM youngteachers yt
    WHERE yt.number_of_loans = (
        SELECT MAX(number_of_loans)
        FROM YoungTeachers
        WHERE School_name = yt.School_name
    )
    ORDER BY number_of_loans DESC
    LIMIT 5";

$result = mysqli_query($conn, $query);

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Professors with Most Books Borrowed</title>
    <style>
        table {
            border-collapse: collapse;
            margin: 0 auto;
            width: 50%;
        }
        h1 {
            text-align: center;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h1>Professors under 40 years old with Most Books Borrowed</h1>
    <?php
    if (mysqli_num_rows($result) > 0) {
        echo "<table>";
        echo "<tr>
                <th>User ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>School Name</th>
                <th>Number of Books Borrowed</th>
              </tr>";
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<tr>";
            echo "<td>" . $row['user_ID'] . "</td>";
            echo "<td>" . $row['first_name'] . "</td>";
            echo "<td>" . $row['last_name'] . "</td>";
            echo "<td>" . $row['School_name'] . "</td>";
            echo "<td>" . $row['number_of_loans'] . "</td>";
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
