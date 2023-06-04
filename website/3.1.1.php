<?php 
session_start();
include("connect.php");


?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Loan Counts by Month and Year</title>
</head>
<body>
    <h1>Loan Counts by Month and Year</h1>
    <form action="3.1.1.php" method="GET">
        <label for="month">Select a Month:</label>
        <select name="month" id="month">
            <option value="1">January</option>
            <option value="2">February</option>
            <option value="3">March</option>
            <option value="4">Apri</option>
            <option value="5">May</option>
            <option value="6">June</option>
            <option value="7">July</option>
            <option value="8">August</option>
            <option value="9">September</option>
            <option value="10">October</option>
            <option value="11">November</option>
            <option value="12">December</option>
            <!-- Add more options for the remaining months -->
        </select>
        <br>
        <label for="year">Select a Year:</label>
        <select name="year" id="year">
            <option value="2022">2022</option>
            <option value="2023">2023</option>
            <option value="2024">2024</option>
            <option value="2024">2025</option>
            <option value="2024">2026</option>
            <!-- Add more options for the desired years -->
        </select>
        <br>
        <button type="submit">Retrieve Loan Counts</button>
    </form>

    <?php
    // Display the loan count results here
   // Check if the form is submitted
if (isset($_GET['month']) && isset($_GET['year'])) {
    $month = $_GET['month'];
    $year = $_GET['year'];

    $query = "SELECT s.school_name, COUNT(*) AS loan_count
              FROM Loan l
              JOIN Book b ON l.Book_ID = b.Book_ID
              JOIN school_unit_manager m ON l.Manager_ID = m.Manager_ID
              JOIN school_unit s ON m.school_id = s.school_id
              WHERE MONTH(l.loan_date) = ? AND YEAR(l.loan_date) = ?
              GROUP BY s.school_name
               ORDER BY loan_count DESC";
        $stmt = mysqli_prepare($conn, $query);
    mysqli_stmt_bind_param($stmt, 'ii', $month, $year);
    mysqli_stmt_execute($stmt);
    $result = mysqli_stmt_get_result($stmt);

    // Check if there are any loan counts
    if (mysqli_num_rows($result) > 0) {
        echo "<h2>Loan Counts for " . date("F", mktime(0, 0, 0, $month, 1)) . " " . $year . "</h2>";
        echo "<ul>";
        
        // Loop through the result and display each loan count
        while ($row = mysqli_fetch_assoc($result)) {
            echo "<li>" . $row['school_name'] . ": " . $row['loan_count'] . "</li>";
        }
        
        echo "</ul>";
    } else {
        echo "<p>No loan counts found for the selected month and year.</p>";
    }

    // Don't forget to close the statement and connection when you're done
    mysqli_stmt_close($stmt);
}
    ?>
<form action="admin_dashboard.php" method="get">
<input type="submit" value="Back">
</form>
</body>
</html>
