<?php
session_start();
include("connect.php");


$query = "SELECT * FROM  YoungTeachers yt
WHERE yt.number_of_loans = (
    SELECT MAX(number_of_loans)
    FROM YoungTeachers
    WHERE School_name = yt.School_name
)
GROUP BY yt.School_name";

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
<h1>Professors under 40 years old with Most Books Borrowed</h1>    <?php
    if (mysqli_num_rows($result) > 0) {
        echo "<table>";
        echo "<tr>
                <th>User ID</th>
                <th>First Name</th>
                <th>Last Name</th>
                <th>School_name</th>
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
</body>
</html>

<?php
// Don't forget to close the database connection
mysqli_close($conn);
?>
