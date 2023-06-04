<?php
// Assuming you have a database connection established
session_start();
include("connect.php");

// Check if the user is logged in as a manager
if (!isset($_SESSION['Manager_ID'])) {
  echo "Access denied";
  exit;
}

// Check if the user ID parameter is provided in the URL
if (!isset($_GET['id'])) {
  echo "User ID not provided";
  exit;
}

$userID = $_GET['id'];
$error = '';
// Get the user data
$userQuery = "SELECT * FROM users WHERE User_ID = ?";
$userStmt = mysqli_prepare($conn, $userQuery);
mysqli_stmt_bind_param($userStmt, "i", $userID);
mysqli_stmt_execute($userStmt);
$userResult = mysqli_stmt_get_result($userStmt);

if (!$userResult || mysqli_num_rows($userResult) == 0) {
  echo "User not found";
  exit;
}

$userData = mysqli_fetch_assoc($userResult);


 // Process form submission
if (isset($_POST['submit'])) {
  // Retrieve the updated user data from the form
  $username = $_POST['username'];
  $firstName = $_POST['first_name'];
  $lastName = $_POST['last_name'];
  $email = $_POST['email'];

  // Validate and sanitize the user input
  $username = mysqli_real_escape_string($conn, $username);
  $firstName = mysqli_real_escape_string($conn, $firstName);
  $lastName = mysqli_real_escape_string($conn, $lastName);
  $email = mysqli_real_escape_string($conn, $email);

  // Check if the new username already exists in the database
  $checkQuery = "SELECT * FROM users WHERE username = ? AND User_ID != ?";
  $checkStmt = mysqli_prepare($conn, $checkQuery);
  mysqli_stmt_bind_param($checkStmt, "si", $username, $userID);
  mysqli_stmt_execute($checkStmt);
  $checkResult = mysqli_stmt_get_result($checkStmt);

  if ($checkResult && mysqli_num_rows($checkResult) > 0) {
    echo 'Username Already Exists.Please choose something else as a username.';
        exit;
  }else{

  // Update the user data in the database
  $updateQuery = "UPDATE users SET username = ?, first_name = ?, last_name = ?, Email = ? WHERE User_ID = ?";
  $updateStmt = mysqli_prepare($conn, $updateQuery);
  mysqli_stmt_bind_param($updateStmt, "ssssi", $username, $firstName, $lastName, $email, $userID);
  mysqli_stmt_execute($updateStmt);

  // Redirect back to the user list page
  header("Location: manage_users.php");
  exit();
  }
}


?>

<!DOCTYPE html>
<html>
<head>
  <title>Edit User</title>
  </head>
<body>
<h2>Edit User</h2>
<?php if (isset($error)) { ?>
    <p style="color: red;"><?php echo $error; ?></p>
  <?php } ?>
      
  <form method="POST" action="edit_user.php?id=<?php echo $userID; ?>">
    <label for="username">Username:</label>
    <input type="text" id="username" name="username" value="<?php echo $userData['username']; ?>"><br><br>
    
    <label for="first_name">First Name:</label>
    <input type="text" id="first_name" name="first_name" value="<?php echo $userData['first_name']; ?>"><br><br>
    
    <label for="last_name">Last Name:</label>
    <input type="text" id="last_name" name="last_name" value="<?php echo $userData['last_name']; ?>"><br><br>
    
    <label for="email">Email:</label>
    <input type="text" id="email" name="email" value="<?php echo $userData['Email']; ?>"><br><br>
    
    <input type="submit" name="submit" value="Save">
  </form>
  <form action="manage_users.php" method="get">
    <input type="submit" value="Back">
</form>
</body>
</html>
