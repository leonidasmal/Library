<!DOCTYPE html>
<html>
<head>
  <title>School List</title>
    <link rel="stylesheet" type="text/css" href="user_dash.css">

  <style>
    /* CSS for hover effect */
    tr:hover {
      background-color: #f5f5f5;
      cursor: pointer;
    }
  </style>
</head>
<body>
  <h1>School List</h1>

  <table>
    <tr>
      <th>School ID</th>
      <th>School Name</th>
    </tr>
    <?php
    include("connect.php");
    session_start();
      // PHP code to retrieve and display the schools
      $schoolQuery = "SELECT School_ID, School_Name FROM school_unit";
      $schoolResult = mysqli_query($conn, $schoolQuery);

      if (!$schoolResult) {
        echo "Query execution failed: " . mysqli_error($conn);
        exit;
      }

      while ($row = mysqli_fetch_assoc($schoolResult)) {
        $schoolID = $row['School_ID'];
        $schoolName = $row['School_Name'];

        echo "<tr onclick=\"window.location='school_details.php?School_ID=$schoolID';\">";
        echo "<td>$schoolID</td>";
        echo "<td>$schoolName</td>";
        echo "</tr>";
      }
    ?>
  </table>
</body>
</html>
