<?php
include("connect.php");
session_start();
$adminID= $_SESSION['Admin_ID'];
if (isset($_POST['approve'])) {
    $id = $_POST['User_ID'];
    $query = "UPDATE users SET status='approved' WHERE User_ID='$id'";
    $result = mysqli_query($conn, $query);
    if ($result) { 
       
        $updateQuery = "UPDATE school_unit_manager SET Admin_ID='$adminID' WHERE User_ID='$id'";
        $updateResult = mysqli_query($conn, $updateQuery);
        header("Location: admin.php");
        echo '<script type="text/javascript">';
        echo 'alert("Operator Approved!");';
        echo 'window.location.reload();';
        echo '</script>';
       
    } else {
        echo "Error approving user: " . mysqli_error($conn);
    }
}

if (isset($_POST['deny'])) {
    $id = $_POST['User_ID'];

    // Delete corresponding rows from students_professors table
    $deleteQuery = "DELETE FROM school_unit_manager WHERE User_ID='$id'";
    $deleteResult = mysqli_query($conn, $deleteQuery);
    if (!$deleteResult) {
        header("Location: admin.php");
        echo "Error deleting corresponding  " . mysqli_error($conn);
        header("Location: manager.php");
        exit;
    }

    // Delete the user from the users table
    $deleteUserQuery = "DELETE FROM users WHERE User_ID='$id'";
    $deleteUserResult = mysqli_query($conn, $deleteUserQuery);
    if ($deleteUserResult) {
        header("Location: admin.php");
        echo '<script type="text/javascript">';
        echo 'alert("Operator denied ");';
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
        <h1>Manager Approval</h1>
        <table>
          <tr>
            <th>Id</th>
            <th>Username</th>
            <th>First Name</th>
            <th>Last Name</th>
            <th>School Name</th>
            <th>Action</th>
          </tr>
          <?php
          $query = "SELECT m.Manager_ID, u.User_ID, u.username, m.first_Name, m.Last_name, su.school_name
                  FROM users u
                  JOIN school_unit_manager m ON u.User_ID = m.User_ID
                  JOIN school_unit su ON m.School_ID = su.School_ID
                  WHERE u.status='pending'";
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
                <td><?php echo $row['first_Name']; ?></td>
                <td><?php echo $row['Last_name']; ?></td>
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