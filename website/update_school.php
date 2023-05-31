<?php
include("connect.php");
session_start();
$schoolID = $_GET['schoolID'];

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (isset($_POST['updateSchool'])) {
    $schoolName = $_POST['schoolName'];
    $address = $_POST['address'];
    $city = $_POST['city'];
    $telephone = $_POST['telephone'];
    $email = $_POST['email'];
    $principalFullName = $_POST['principalFullName'];

    // Perform the necessary database update for the school
    $updateQuery = "UPDATE school_unit SET 
      school_name = '$schoolName',
      address = '$address',
      city = '$city',
      telephone = '$telephone',
      email = '$email',
      principal_fullname = '$principalFullName'
      WHERE School_ID = $schoolID";
      
    $updateResult = mysqli_query($conn, $updateQuery);

    if ($updateResult) {
      echo "School updated successfully.";
    } else {
      echo "Error updating school: " . mysqli_error($conn);
    }
  }
}

// Retrieve the existing school attributes
$existingSchoolQuery = "SELECT school_name, address, city, telephone, email, principal_fullname 
  FROM school_unit 
  WHERE School_ID = $schoolID";
$existingSchoolResult = mysqli_query($conn, $existingSchoolQuery);

if ($existingSchoolResult && mysqli_num_rows($existingSchoolResult) > 0) {
  $row = mysqli_fetch_assoc($existingSchoolResult);
  $existingSchoolName = $row['school_name'];
  $existingAddress = $row['address'];
  $existingCity = $row['city'];
  $existingTelephone = $row['telephone'];
  $existingEmail = $row['email'];
  $existingPrincipalFullName = $row['principal_fullname'];
} else {
  echo "Error retrieving existing school details: " . mysqli_error($conn);
  exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Update School</title>
</head>
<body>
  <h1>Update School</h1>
  <form method="POST" action="">
    <label for="schoolName">School Name:</label>
    <input type="text" id="schoolName" name="schoolName" value="<?php echo $existingSchoolName; ?>" required><br><br>
    
    <label for="address">Address:</label>
    <input type="text" id="address" name="address" value="<?php echo $existingAddress; ?>" required><br><br>
    
    <label for="city">City:</label>
    <input type="text" id="city" name="city" value="<?php echo $existingCity; ?>" required><br><br>
    
    <label for="telephone">Telephone:</label>
    <input type="text" id="telephone" name="telephone" value="<?php echo $existingTelephone; ?>" required><br><br>
    
    <label for="email">Email of the School:</label>
    <input type="email" id="email" name="email" value="<?php echo $existingEmail; ?>" required><br><br>

    <label for="principalFullName">Principal FullName:</label>
    <input type="text" id="principalFullName" name="principalFullName" value="<?php echo $existingPrincipalFullName; ?>" required><br><br>
    
   
    <button type="submit" name="updateSchool">Update School</button>
  </form>
</body>
</html>