<?php
function restoreDatabaseTables($dbHost, $dbUsername, $dbPassword, $dbName, $filePath){
    // Connect & select the database
    $db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

    // Check for connection errors
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    // Check if file is uploaded successfully
    if (!is_uploaded_file($filePath)) {
        die("Error uploading file.");
    }

    // Read the SQL file
    $sql = file_get_contents($filePath);

    // Execute the SQL queries
    if ($db->multi_query($sql)) {
        do {
            // Store the result
            if ($result = $db->store_result()) {
                $result->free();
            }
        } while ($db->more_results() && $db->next_result());
    } else {
        // Display error message if any
        echo "Error restoring database: " . $db->error;
        return false;
    }

    return true;
}

function dropAllTables($dbHost, $dbUsername, $dbPassword, $dbName){
    // Connect & select the database
    $db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

    // Check for connection errors
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
    }

    // Get all table names from the database
    $sql = "SHOW TABLES";
    $result = $db->query($sql);

    // Enable foreign key checks
    $db->query('SET FOREIGN_KEY_CHECKS = 0;');

    while ($row = $result->fetch_row()) {
        $dropTableSql = "DROP TABLE IF EXISTS " . $row[0];
        $db->query($dropTableSql);
    }

    $result->free();
    $db->close();
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbHost     = 'localhost';
    $dbUsername = 'root';
    $dbPassword = '';
    $dbName     = 'library';
    $filePath   = $_FILES['sql_file']['tmp_name'];

    dropAllTables($dbHost, $dbUsername, $dbPassword, $dbName);

    if (restoreDatabaseTables($dbHost, $dbUsername, $dbPassword, $dbName, $filePath)) {
        header("Location: admin_dashboard.php");
    } else {
        echo "Database reset failed.";
    }
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Database Reset</title>
  <link rel="stylesheet" type="text/css" href="reset_DB.css">
</head>
<body>
<header>
  <div class="logo">Library</div>
  <nav>
    <ul>
      <li><a href="admin_dashboard.php">Home</a></li>
      <li><a href="front_page.php">Log out</a></li>
    </ul>
  </nav>
</header>
<h2>Database Reset</h2>
<p class="center-text">Please select the SQL file that you want to use to reset the library database.</p>
<div class="form-group center">
    <form action="" method="POST" enctype="multipart/form-data">
        <input type="file" name="sql_file">
        <button type="submit">Reset Database</button>
    </form>
</div>
<footer>
  <p>&copy; 2023 Library. All rights reserved.</p>
</footer>
<form action="admin_dashboard.php" method="get">
<input type="submit" value="Back">
</form>
</body>
</html>
