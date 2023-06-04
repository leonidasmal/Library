<?php
// Assuming you have a database connection established
session_start();
include("connect.php");

// Check if the user is logged in as a manager
if (!isset($_SESSION['Manager_ID'])) {
    echo "Access denied";
    exit;
}

$searchUsername = isset($_POST['username']) ? $_POST['username'] : '';
$searchCategory = isset($_POST['category']) ? $_POST['category'] : '';


// Retrieve the average ratings per borrower and category
$averageRatingsQuery = "
    SELECT
        sp.studprof_id AS Borrower_ID,
        u.username AS username,
        c.Category_ID,
        c.category AS Category,
        AVG(r.likert_scale) AS Average_Rating
    FROM
        Review r
    JOIN
        Book b ON r.Book_ID = b.Book_ID
    JOIN
        Book_Category bc ON b.Book_ID = bc.Book_ID
    JOIN
        Category c ON bc.Category_ID = c.Category_ID
    JOIN
        students_professors sp ON r.User_ID = sp.studprof_id
    JOIN
        Users u ON sp.User_ID = u.User_ID
    WHERE
        r.approved = 1";


    // Add search conditions based on the provided criteria
    if (!empty($searchUsername)) {
            $averageRatingsQuery .= " AND u.username LIKE '%$searchUsername%'";
        }
        
        if (!empty($searchCategory)) {
            $averageRatingsQuery .= " AND u.last_name LIKE '%$searchCategory%'";
        }
        
        $averageRatingsQuery .= " GROUP BY sp.studprof_id, c.Category_ID";
        
        $averageRatingsResult = mysqli_query($conn, $averageRatingsQuery);
        

if (!$averageRatingsResult) {
    echo "Query execution failed: " . mysqli_error($conn);
    exit;
}
?>

<!DOCTYPE html>
<html>
<head>
    <title>Average Ratings</title>
    <style>
        table {
            border-collapse: collapse;
            margin: 0 auto;
            width: 50%;
        }
        th, td {
            padding: 8px;
            text-align: left;
            border-bottom: 1px solid #ddd;
        }
        th {
            background-color: #f2f2f2;
        }
    </style>
</head>
<body>
    <h2>Average Ratings per Borrower and Category</h2>

    <form method="POST">
    <input type="text" name="username" placeholder="Search by Username" value="<?php echo $searchUsername; ?>">
    <input type="text" name="category" placeholder="Search by Category" value="<?php echo $searchCategory; ?>">
    <button type="submit">Search</button>
  </form>
    <table>
        <thead>
            <tr>
                <th>username</th>
                <th>Category</th>
                <th>Average Rating</th>
            </tr>
        </thead>
        <tbody>
            <?php
            // Display average ratings in the table
            while ($rating = mysqli_fetch_assoc($averageRatingsResult)) {
                ?>
                <tr>

                    <td><?php echo $rating['username']; ?></td>
                    <td><?php echo $rating['Category']; ?></td>
                    <td><?php echo $rating['Average_Rating']; ?></td>
                </tr>
                <?php
            }
            ?>
        </tbody>
    </table>
    <form action="operator_manager_dashboard.php" method="get">
    <input type="submit" value="Back">
</form>
</body>
</html>
