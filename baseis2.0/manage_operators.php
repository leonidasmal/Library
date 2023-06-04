<?php
// Assuming you have a database connection established
session_start();
include("connect.php");

// Check if the user is logged in as a manager
if (!isset($_SESSION['Admin_ID'])) {
  echo "Access denied";
  exit;
}

$adminID = $_SESSION['Admin_ID'];

if (isset($_POST['user_id']) && isset($_POST['current_status'])) {
    // Get the user ID and current status from the form submission
    $userID = $_POST['user_id'];
    $currentStatus = $_POST['current_status'];
  
    // Perform the update based on the current status
    $newStatus = ($currentStatus === "1") ? "2" : "1";
  
    // Update the user's approval status in the database
    $updateQuery = "UPDATE users SET approved = ? WHERE User_ID = ?";
    $updateStmt = mysqli_prepare($conn, $updateQuery);
    mysqli_stmt_bind_param($updateStmt, "ii", $newStatus, $userID);
    mysqli_stmt_execute($updateStmt);
  
    // Redirect back to the user list page
    header("Location: manage_operators.php");
    exit;
  }

// Get the users associated with the manager's school
$usersQuery = "SELECT m.Manager_ID, u.User_ID, u.username, u.first_Name, u.Last_name,u.Email,u.approved ,su.school_name
FROM users u
JOIN school_unit_manager m ON u.User_ID = m.User_ID
JOIN school_unit su ON m.School_ID = su.School_ID";

$result = mysqli_query($conn, $usersQuery);

if (!$result || mysqli_num_rows($result) == 0) {
  echo "Query execution failed or no users found: " . mysqli_error($conn);
  exit;
}


?>

<!DOCTYPE html>
<html>
<head>
  <title>Operators List</title>
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

    .edit-button, {
      padding: 6px 12px;
      margin-right: 5px;
    }
  </style>
  <script>
function toggleStatus(user_id, currentStatus) {
  var result;
  if (currentStatus === "1") {
    result = confirm("Are you sure you want to deactivate this user?");
  } else {
    result = confirm("Are you sure you want to activate this user?");
  }

  if (result) {
    // Submit the form to toggle the approval status
    var form = document.getElementById("toggle_status_form_" + user_id);
    form.submit();
  }
}
</script>

</head>
<body>
  <h2>Operators List</h2>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Username</th>
        <th>Name</th>
        <th>Last name</th>
        <th>Email</th>
        <th>Status</th>
        <th>School</th>
        
      </tr>
    </thead>
    <tbody>
      <?php
      // Display users in the table
      while ($user = mysqli_fetch_assoc($result)) {
        ?>
        <tr>
          <td><?php echo $user['User_ID']; ?></td>
          <td><?php echo $user['username']; ?></td>
          <td><?php echo $user['first_Name']; ?></td>
          <td><?php echo $user['Last_name']; ?></td>
          <td><?php echo $user['Email']; ?></td>
          <td><?php echo ($user['approved'] == 1) ? 'Active' : (($user['approved'] == 2) ? 'Deactivated' : 'Not yet registered'); ?></td>        
          <td><?php echo $user['school_name'];?></td>  

          <td class="button-container">
            <form id="toggle_status_form_<?php echo $user['User_ID']; ?>" method="post" action="">
                <input type="hidden" name="user_id" value="<?php echo $user['User_ID']; ?>">
                <input type="hidden" name="current_status" value="<?php echo $user['approved']; ?>">
                <button class="toggle-status-button" onclick="toggleStatus(<?php echo $user['User_ID']; ?>, '<?php echo $user['approved']; ?>')">
                <?php echo ($user['approved'] === "1") ? "Deactivate" : "Activate"; ?>
                </button>
            </form>
            </td>

          </td>
        </tr>
        <?php
      }
      ?>
    </tbody>
  </table>
  <form action="admin_dashboard.php" method="get">
<input type="submit" value="Back">
</form>
  
</body>
</html>
