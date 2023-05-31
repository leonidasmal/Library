<?php
function restoreDatabaseTables($dbHost, $dbUsername, $dbPassword, $dbName, $filePath){
    // Connect & select the database
    $db = new mysqli($dbHost, $dbUsername, $dbPassword, $dbName);

    // Check for connection errors
    if ($db->connect_error) {
        die("Connection failed: " . $db->connect_error);
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

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $dbHost     = 'localhost';
    $dbUsername = 'root';
    $dbPassword = '';
    $dbName     = 'library';
    $filePath   = $_FILES['sql_file']['tmp_name'];

    if (restoreDatabaseTables($dbHost, $dbUsername, $dbPassword, $dbName, $filePath)) {
        echo "Database reset successful.";
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
<header>
  <div class="logo">Library</div>
  <nav>
    <ul>
    <li><a href="admin_dashboard.php">Home</a></li>
      <li><a href="#">Library Events</a></li>
     
      <li><a href="front_page.php">Log out</a></li> <!-- Add logout link with query parameter -->    </ul>
  </nav>
</header>
<body>
    <h2>Database Reset</h2>
    <p class="center-text">Please select the SQL file that you want to use to reset the database library.</p>
        <div class="form-group center">
    <form action="backup.php" method="POST" enctype="multipart/form-data">
        <input type="file" name="sql_file">
        <button type="submit">Reset Database</button>
    </form>
    <footer>
  <p>&copy; 2023 Library. All rights reserved.</p>
</footer>
</body>
</html>

