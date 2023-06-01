<?php
// Assuming you have a database connection established
session_start();
include("connect.php");

// Check if the user is logged in as a manager
if (!isset($_SESSION['Manager_ID'])) {
  echo "Access denied";
  exit;
}

$managerID = $_SESSION['Manager_ID'];

// Get the manager's school ID
$managerQuery = "SELECT school_ID FROM school_unit_manager WHERE manager_ID = ?";
$managerStmt = mysqli_prepare($conn, $managerQuery);
mysqli_stmt_bind_param($managerStmt, "i", $managerID);
mysqli_stmt_execute($managerStmt);
$managerResult = mysqli_stmt_get_result($managerStmt);

if ($managerResult && mysqli_num_rows($managerResult) > 0) {
  $managerData = mysqli_fetch_assoc($managerResult);
  $managerSchoolID = $managerData['school_ID'];
} else {
  echo "No manager data found";
  exit;
}

// Get the users associated with the manager's school
$usersQuery = "SELECT DISTINCT u.* FROM users u
               INNER JOIN students_professors sp ON u.User_ID = sp.User_ID
               INNER JOIN school_unit_manager som ON sp.school_ID = som.school_ID
               WHERE som.school_ID = ?";
$usersStmt = mysqli_prepare($conn, $usersQuery);
mysqli_stmt_bind_param($usersStmt, "i", $managerSchoolID);
mysqli_stmt_execute($usersStmt);
$usersResult = mysqli_stmt_get_result($usersStmt);

if (!$usersResult || mysqli_num_rows($usersResult) == 0) {
  echo "Query execution failed or no users found: " . mysqli_error($conn);
  exit;
}

// Process delete request
if (isset($_POST['submit_del'])) {
  $id = $_POST['User_ID']; // Get the ID of the user to delete

  // Validate and sanitize the user input
  $id = mysqli_real_escape_string($conn, $id);
  if (!is_numeric($id)) {
    echo "Invalid user ID";
    exit;
  }

  // Check if the user exists before deleting
  $checkQuery = "SELECT * FROM users WHERE User_ID = ?";
  $checkStmt = mysqli_prepare($conn, $checkQuery);
  mysqli_stmt_bind_param($checkStmt, "i", $id);
  mysqli_stmt_execute($checkStmt);
  $checkResult = mysqli_stmt_get_result($checkStmt);

  if (!$checkResult || mysqli_num_rows($checkResult) == 0) {
    echo "User not found";
    exit;
  }
  
  // Get the studprof_id of the deleted user
  $studProfIDQuery = "SELECT studprof_id FROM students_professors WHERE User_ID = ?";
  $studProfIDStmt = mysqli_prepare($conn, $studProfIDQuery);
  mysqli_stmt_bind_param($studProfIDStmt, "i", $id);
  mysqli_stmt_execute($studProfIDStmt);
  $studProfIDResult = mysqli_stmt_get_result($studProfIDStmt);

  if ($studProfIDResult && mysqli_num_rows($studProfIDResult) > 0) {
    $studProfIDData = mysqli_fetch_assoc($studProfIDResult);
    $deletedStudProfID = $studProfIDData['studprof_id'];
    $savedStudProfID = $deletedStudProfID;
    echo "The studprof_id of the deleted user is: " . $deletedStudProfID;
  } else {
    echo "Studprof_id not found for the deleted user";
  }

  // Start a transaction
  mysqli_begin_transaction($conn);

  // Delete from reviews table
  $reviewsQuery = "DELETE FROM review WHERE User_ID = ?";
  $reviewsStmt = mysqli_prepare($conn, $reviewsQuery);
  mysqli_stmt_bind_param($reviewsStmt, "i", $id);
  mysqli_stmt_execute($reviewsStmt);

  // Delete from stud_prof table
  $studProfQuery = "DELETE FROM reservation WHERE studprof_id = ?";
  $studProfStmt = mysqli_prepare($conn, $studProfQuery);
  mysqli_stmt_bind_param($studProfStmt, "i", $savedStudProfID);
  mysqli_stmt_execute($studProfStmt);

  // Delete from stud_prof table
  $studProfQuery = "DELETE FROM borrower_card WHERE studprof_id = ?";
  $studProfStmt = mysqli_prepare($conn, $studProfQuery);
  mysqli_stmt_bind_param($studProfStmt, "i", $savedStudProfID);
  mysqli_stmt_execute($studProfStmt);

  // Delete from stud_prof table
  $studProfQuery = "DELETE FROM loan WHERE studprof_id = ?";
  $studProfStmt = mysqli_prepare($conn, $studProfQuery);
  mysqli_stmt_bind_param($studProfStmt, "i", $savedStudProfID);
  mysqli_stmt_execute($studProfStmt);

  // Delete from stud_prof table
  $studProfQuery = "DELETE FROM students_professors WHERE User_ID = ?";
  $studProfStmt = mysqli_prepare($conn, $studProfQuery);
  mysqli_stmt_bind_param($studProfStmt, "i", $id);
  mysqli_stmt_execute($studProfStmt);

  // Delete from users table
  $usersQuery = "DELETE FROM users WHERE User_ID = ?";
  $usersStmt = mysqli_prepare($conn, $usersQuery);
  mysqli_stmt_bind_param($usersStmt, "i", $id);
  mysqli_stmt_execute($usersStmt);

  // Commit the transaction
  mysqli_commit($conn);

  // Redirect to the user list page
  header("Location: manage_users.php");
  exit();
}
?>

<!DOCTYPE html>
<html>
<head>
  <title>User List</title>
  <style>
    table {
      border-collapse: collapse;
      margin: 0 auto; /* Add this line to center the table */
      width: 50%;
    }

    th, td {
      padding: 8px;
      text-align: left;
      border-bottom: 1px solid #ddd;
    }

    th {
      background-color: #f2f2f2;
    }

    .button-container {
      display: inline-block;
    }

    .edit-button, .delete-button {
      padding: 6px 12px;
      margin-right: 5px;
    }
  </style>
  
</head>
<body>
  <h2>User List</h2>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Name</th>
        <th>Last name</th>
        <th>Email</th>
        <th>Status</th>
        <th>Action</th>
      </tr>
    </thead>
    <tbody>
      <?php
      // Display users in the table
      while ($user = mysqli_fetch_assoc($usersResult)) {
        ?>
        <tr>
          <td><?php echo $user['User_ID']; ?></td>
          <td><?php echo $user['username']; ?></td>
          <td><?php echo $user['first_name']; ?></td>
          <td><?php echo $user['last_name']; ?></td>
          <td><?php echo $user['Email']; ?></td>
          <td><?php echo ($user['approved'] == 1) ? 'Active' : (($user['approved'] == 2) ? 'Deactivated' : 'Not yet registered'); ?></td>
          <td class="button-container">
            <button class="edit-button" onclick="editUser(<?php echo $user['User_ID']; ?>)">Edit</button>
            <form id="delete-form-<?php echo $user['User_ID']; ?>" method="POST" action="manage_users.php">
    <input type="hidden" name="User_ID" value="<?php echo $user['User_ID']; ?>">
        <button class="delete-button" type="submit" name="submit_del">Delete</button>
</form>

          </td>
        </tr>
        <?php
      }
      ?>
    </tbody>
  </table>
  
  <script>
    function confirmDelete(user_id) {
      var result = confirm("Are you sure you want to delete this user?");
      if (result) {
        document.getElementById("delete-form-" + user_id).submit();
      }
    }
    
    function editUser(user_id) {
      // Implement the edit functionality here
      // You can redirect the user to an edit page or perform an AJAX request
      // based on your application's requirements
    }
  </script>
</body>
</html>
