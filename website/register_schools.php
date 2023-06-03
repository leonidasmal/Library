<?php
        session_start();
        include("connect.php");
        $adminID = $_SESSION['Admin_ID'];
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Admin_dashboard</title>
  <link rel="stylesheet" type="text/css" href="user_dash.css">
<head>
  <title>Available Schools</title>
  <style>
   
   .table-container {
      width: 80%;
      margin: 0 auto; /* Center align the table */
    }
    table {
      border-collapse: collapse;
      width: 100%;
    }
    th, td {
      border: 1px solid #ddd;
      padding: 8px;
      text-align: left;
    }
    th {
      background-color: #f2f2f2;
    }
    .button-container {
      margin-top: 10px;
      text-align: right;
    }
    .button-container button {
      margin-right: 5px;
    }
  </style>
</head>

<body>
    <header>
  <div class="logo">Library</div>
  <nav>
    <ul>
    <li><a href="admin_dashboard.php">Home</a></li>
      <li><a href="#">Library Events</a></li>
     
      <li><a href="front_page.php">Log out</a></li> <!-- Add logout link with query parameter -->    </ul>
  </nav>
</header>

  <div class="table-container">
    <h2>Available Schools</h2>
    <table>
      <thead>
        <tr>
          <th>School ID</th>
          <th>Name</th>
          <th>Address</th>
          <th>City</th>
          <th>Telephone</th>
          <th>Email of the School</th>
          <th>Principal's Fullname</th>
          <th>Action</th>
        </tr>
      </thead>
      <tbody>
        <?php
        
        include("connect.php");

        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['schoolID'])) {
          $_SESSION['School_ID'] = $_POST['schoolID'];
          header("Location: delete_school.php"); // Redirect to make_reservation.php after storing the book ID in the session
          exit;
        }
        if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['E_schoolID'])) {
          $_SESSION['School_ID'] = $_POST['E_schoolID'];
          header("Location: update_school.php"); // Redirect to make_reservation.php after storing the book ID in the session
          exit;
        }
       
        // Query to retrieve available schools
        $sql = "SELECT School_ID, School_name, address, city,Telephone,email,principal_fullname FROM school_unit";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
            echo "<tr>";
            echo "<td>" . $row["School_ID"] . "</td>";
            echo "<td>" . $row["School_name"] . "</td>";
            echo "<td>" . $row["address"] . "</td>";
            echo "<td>" . $row["city"] . "</td>";
            echo "<td>" . $row["Telephone"] . "</td>";
            echo "<td>" . $row["email"] . "</td>";
            echo "<td>" . $row["principal_fullname"] . "</td>";
            echo "<td>";
            echo "<form action='' method='post'>";
            echo "<input type='hidden' name='E_schoolID' value='" . $row["School_ID"] . "'>";
            echo "<input type='submit' value='Edit School'>";
            echo "</form>";
            echo "<form action='' method='post'>";
            echo "<input type='hidden' name='schoolID' value='" . $row["School_ID"] . "'>";
            echo "<input type='submit' value='Delete School'>";
            echo "</form>";
            echo "</td>";
            echo "</tr>";
          }
        } else {
          echo "<tr><td colspan='4'>No schools found</td></tr>";
        }

        // Close the connection
        $conn->close();
        ?>
      </tbody>
    </table>

    <div class="button-container">
      <button onclick="addSchool()">Add School</button>
    </div>
  </div>

  <script>

    function addSchool() {
      // Implement the logic to add a new school
      // Redirect the user to the add school page or display a form to add the school details
      window.location.href = "add_school.php";
    }
  </script>
<footer>
  <p>&copy; 2023 Library. All rights reserved.</p>
</footer>

</body>
</html>
