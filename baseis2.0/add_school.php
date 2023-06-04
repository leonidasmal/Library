<?php
session_start();
include("connect.php");
$adminID = $_SESSION['Admin_ID'];

// Retrieve form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST["name"];
  $address = $_POST["address"];
  $city = $_POST["city"];
  $telephone = $_POST["telephone"];
  $email = $_POST["email"];
  $principal = $_POST["principal"];

  // Check if the school already exists in the database
  $checkQuery = "SELECT * FROM school_unit WHERE School_name = ? OR address = ? OR City = ? OR Telephone = ? OR email = ?";
  $checkStatement = mysqli_prepare($conn, $checkQuery);
  mysqli_stmt_bind_param($checkStatement, "sssss", $name, $address, $city, $telephone, $email);
  mysqli_stmt_execute($checkStatement);
  $checkResult = mysqli_stmt_get_result($checkStatement);

  if ($checkResult->num_rows > 0) {
    echo "The school already exists in the database.";
  } else {
    // Start a transaction
    mysqli_begin_transaction($conn);

    // Perform the insertion
    $insertQuery = "INSERT INTO school_unit (School_name, address, City, Telephone, email, principal_fullname, Admin_ID) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $insertStatement = mysqli_prepare($conn, $insertQuery);
    mysqli_stmt_bind_param($insertStatement, "ssssssi", $name, $address, $city, $telephone, $email, $principal, $adminID);
    $insertResult = mysqli_stmt_execute($insertStatement);

    if ($insertResult) {
      mysqli_commit($conn); // Commit the transaction
      echo "School added successfully.";
    } else {
      // Rollback the transaction
      mysqli_rollback($conn);
      echo "Error adding school: " . mysqli_error($conn);
    }
  }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Add School</title>
  <link rel="stylesheet" type="text/css" href="user_dash.css">
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
      <li><a href="view_account.php">My Account</a></li>
      <li><a href="#">Library Events</a></li>
      <li><a href="front_page.php">Log Out</a></li>
    </ul>
  </nav>
</header>
<section class="main-content">
  <div class="container">
    <h2>Add School</h2>
    <form action="add_school.php" method="POST">
      <label for="name">School Name:</label>
      <input type="text" id="name" name="name" required><br><br>

      <label for="address">Address:</label>
      <input type="text" id="address" name="address" required><br><br>

      <label for="city">City:</label>
      <input type="text" id="city" name="city" required><br><br>

      <label for="telephone">Telephone:</label>
      <input type="phone" id="telephone" name="telephone" required><br><br>

      <label for="email">Email:</label>
      <input type="email" id="email" name="email" required><br><br>

      <label for="principal">Principal's Fullname:</label>
      <input type="text" id="principal" name="principal" required><br><br>

      <input type="submit" value="Add School">
    </form>
  </div>
</section>
<footer>
  <!-- Footer content here -->
</footer>
</body>
</html>