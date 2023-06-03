<?php
session_start();
include("connect.php");
$adminID = $_SESSION['Admin_ID'];
var_dump($adminID);

// Retrieve form data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $name = $_POST["name"];
  $address = $_POST["address"];
  $city = $_POST["city"];
  $telephone = $_POST["telephone"];
  $email = $_POST["email"];
  $principal = $_POST["principal"];

  // Check if the school already exists in the database
  $checkQuery = "SELECT * FROM school_unit WHERE School_name = '$name' OR address = '$address' OR City = '$city' OR Telephone = '$telephone' OR email = '$email'";
  $checkResult = mysqli_query($conn, $checkQuery);

  if ($checkResult->num_rows > 0) {
    echo "The school already exists in the database.";
  } else {
    // Start a transaction
    mysqli_begin_transaction($conn);

    // Perform the insertion
    $insertQuery = "INSERT INTO school_unit (School_name, address, City, Telephone, email, principal_fullname) VALUES ('$name', '$address', '$city', '$telephone', '$email', '$principal')";
    $insertResult = mysqli_query($conn, $insertQuery);

    if ($insertResult) {
      echo "School added successfully.";

      // Fetch the ID of the last inserted school
      $schoolID = mysqli_insert_id($conn);

      // Insert into register table
      $insertRegisterQuery = "INSERT INTO register (Admin_id, school_id) VALUES ('$adminID', '$schoolID')";
      $insertRegisterResult = mysqli_query($conn, $insertRegisterQuery);

      if ($insertRegisterResult) {
        // Commit the transaction
        mysqli_commit($conn);
        echo "Registration successful.";
        header("Location: register_schools.php");
        exit();
      } else {
        // Rollback the transaction
        mysqli_rollback($conn);
        echo "Error registering: " . mysqli_error($conn);
      }
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

  <head>

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
    <li><a href="view_account.php">My Account</a></li> <!-- Updated link -->
      <li><a href="#">Library Events</a></li>
      <li><a href="front_page.php">Log Out</a></li> <!-- Add logout link with query parameter -->    </ul>
  
  </nav>
  <style>
    footer {
     
       text-align: left;
      margin-top: 650px;
    }
  </style>
</header>

</head>
<body>
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
</body>
</html>
