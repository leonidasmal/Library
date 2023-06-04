<?php
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Include the database connection file
    $servername = "localhost";
    $username = "root";
    $password = "";
    $dbname = "library";

    $conn = mysqli_connect($servername, $username, $password, $dbname);
    $conn->set_charset("utf8");

    $tables = array('School_Unit', 'Users', 'administrator','School_Unit_Manager','Book_Language',
    'Book','Author','Book_Author','Category','Book_Category','School_Book','Register','students_professors','Borrower_Card',
    'Loan','Reservation','Review');
    $sql = "SHOW TABLES";
    $query = $conn->query($sql);
    while ($row = $query->fetch_row()) {
        $tables[] = $row[0];
    }
    
    $outsql = '';
    foreach ($tables as $table) {
        $sql = "SHOW CREATE TABLE $table";
        $query = $conn->query($sql);
        $row = $query->fetch_row();

        $outsql .= "\n\n" . $row[1] . ";\n\n";

        $sql = "SELECT * FROM $table";
        $query = $conn->query($sql);

        $columnCount = $query->field_count;

        for ($i = 0; $i < $columnCount; $i++) {
            while ($row = $query->fetch_row()) {
                $outsql .= "INSERT INTO $table VALUES(";
                for ($j = 0; $j < $columnCount; $j++) {
                    $row[$j] = $row[$j];

                    if (isset($row[$j])) {
                        $outsql .= '"' . $row[$j] . '"';
                    } else {
                        $outsql .= '""';
                    }
                    if ($j < ($columnCount - 1)) {
                        $outsql .= ',';
                    }
                }
                $outsql .= ");\n";
            }
        }

        $outsql .= "\n";
    }

    if (!empty($outsql)) {
        $backup_file_name = "database_backup.sql";
        $fileHandler = fopen($backup_file_name, 'w+');
        fwrite($fileHandler, $outsql);
        fclose($fileHandler);

        // Download the SQL backup file to the browser
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename=' . basename($backup_file_name));
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($backup_file_name));
        ob_clean();
        flush();
        readfile($backup_file_name);
        unlink($backup_file_name); // Remove the SQL file after download
    } else {
        echo "Database backup failed.";
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
 
  <link rel="stylesheet" type="text/css" href="backup.css">
</head>
<header>
  <div class="logo">Library</div>
  <nav>
    <ul>
      <li><a href="admin_dashboard.php">Home</a></li>
      <li><a href="#">Library Events</a></li>
      <li><a href="front_page.php">Log out</a></li>
      <!-- Add logout link with query parameter -->
    </ul>
  </nav>
</header>
<body>
  <div class="container">
    <h2>Database Backup</h2>
    <form action="backup.php" method="POST">
      <div class="form-group">
        <input type="hidden" name="backup_file" value="database_backup.sql">
      </div>
      <div class="form-group">
        <button type="submit">Backup Database</button>
      </div>
    </form>
  </div>
  <footer>
  <p>&copy; 2023 Library. All rights reserved.</p>
</footer>
</body>
</html>
