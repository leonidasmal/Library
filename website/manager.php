<?php
include("connect.php");
session_start();
$managerID = $_SESSION['Manager_ID'];

var_dump($managerID);
if (isset($_POST['approve'])) {
  $id = $_POST['User_ID'];
  $query = "UPDATE users SET approved='1' WHERE User_ID='$id'";
  $result = mysqli_query($conn, $query);

  if ($result) {
      // User appoved update successful
      $managerID = $_SESSION['Manager_ID'];
      $updateManagerQuery = "UPDATE students_professors SET Manager_ID='$managerID' WHERE User_ID='$id'";
      $updateManagerResult = mysqli_query($conn, $updateManagerQuery);

      if ($updateManagerResult) {
          // Both updates successful
          header("Location: manager.php");
          echo '<script type="text/javascript">';
          echo 'alert("User Approved!");';
          echo 'window.location.reload();';
          echo '</script>';
      } else {
          // Error updating students_professors table
          echo "Error updating students_professors: " . mysqli_error($conn);
      }
  } else {
      // Error updating user approved
      echo "Error approving user: " . mysqli_error($conn);
  }
}



if (isset($_POST['deny'])) {
    $id = $_POST['User_ID'];

    // Delete corresponding rows from students_professors table
    $deleteQuery = "DELETE FROM students_professors WHERE User_ID='$id'";
    $deleteResult = mysqli_query($conn, $deleteQuery);
    if (!$deleteResult) {
        echo "Error deleting corresponding rows from students_professors table: " . mysqli_error($conn);
        header("Location: manager.php");
        exit;
    }

    // Delete the user from the users table
    $deleteUserQuery = "DELETE FROM users WHERE User_ID='$id'";
    $deleteUserResult = mysqli_query($conn, $deleteUserQuery);
    if ($deleteUserResult) {
        header("Location: manager.php");
        echo '<script type="text/javascript">';
        echo 'alert("User denied ");';
        echo 'window.location.reload();';
        echo '</script>';
    } else {
        echo "Error deleting user: " . mysqli_error($conn);
    }
}
?>



<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="manager_app.css">

    <title>Manager Approval</title>
  </head>
  <body>
    <div class="center">
      <div class="container">
        <h1>User Registration</h1>
        <table>
          <tr>
            <th>Id</th>
            <th>Username</th>
            <th>Role</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>School Name</th>
            <th>Action</th>
          </tr>
          <?php
          $query = "SELECT u.User_ID, u.username, sp.is_Professor, u.first_name, u.last_name, su.school_name
          FROM users u
          JOIN students_professors sp ON u.User_ID = sp.User_ID
          JOIN school_unit su ON sp.school_ID = su.school_ID
          JOIN school_unit_manager sm ON sm.Manager_ID = '$managerID' AND sm.School_ID = su.school_ID
          WHERE u.approved='0'";
          $result = mysqli_query($conn, $query);
          if (!$result) {
              echo "Error fetching user registrations: " . mysqli_error($conn);
              exit;
          }
          while ($row = mysqli_fetch_assoc($result)):
              ?>
              <tr>
                <td><?php echo $row['User_ID']; ?></td>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['is_Professor'] ? 'Professor' : 'Student'; ?></td>
                <td><?php echo $row['first_name']; ?></td>
                <td><?php echo $row['last_name']; ?></td>
                <td><?php echo $row['school_name']; ?></td>
                <td>
                  <form action="manager.php" method="POST">
                    <input type="hidden" name="User_ID" value="<?php echo $row['User_ID']; ?>">
                    <button type="submit" name="approve">Approve</button>
                    <button type="submit" name="deny" class="btn-red">Deny</button>
                  </form>
                </td>
              </tr>
          <?php endwhile; ?>
        </table>
      </div>
    </div>
  </body>
</html>
