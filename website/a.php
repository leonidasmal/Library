<?php
include("connect.php");
session_start();
$managerID = $_SESSION['Manager_ID'];
var_dump($managerID);
if (isset($_POST['approve'])) {
  $reviewID = $_POST['review_id'];
  // Query to update the approved status
  $query = "UPDATE review SET approved = '1' WHERE review_id = ?";
  $stmt = mysqli_prepare($conn, $query);

  if ($stmt) {
    mysqli_stmt_bind_param($stmt, 's', $reviewID);
    mysqli_stmt_execute($stmt);

    if (mysqli_stmt_affected_rows($stmt) > 0) {
      // Review approved update successful
      echo '<script type="text/javascript">';
      echo 'alert("Review Approved!");';
      echo 'window.location.href = "manager.php?Manager_ID=' . $managerID . '";';
            echo '</script>';
    } else {
      // Error updating review approved status
      echo "Error updating review approved status: " . mysqli_error($conn);
    }

    mysqli_stmt_close($stmt);
  } else {
    // Error in prepared statement for approved status update
    echo "Error preparing statement for approved status update: " . mysqli_error($conn);
  }
}

if (isset($_POST['deny'])) {
  $reviewID = $_POST['review_id'];

  // Delete the review from the reviews table
  $deleteQuery = "DELETE FROM review WHERE review_id='$reviewID'";
  $deleteResult = mysqli_query($conn, $deleteQuery);
  if ($deleteResult) {
      echo '<script type="text/javascript">';
      echo 'alert("Review denied ");';
      echo 'window.location.href = "Manager.php?manager_ID=' . $managerID . '";';
      echo '</script>';
  } else {
      echo "Error deleting review: " . mysqli_error($conn);
  }
}
?>

<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" type="text/css" href="manager_app.css">
    <title>Review Approval</title>
  </head>
  <body>
    <div class="center">
      <div class="container">
        <h1>Review Approval</h1>
        <!-- Print manager_ID -->
        <p>Manager ID: <?php echo $managerID; ?></p>
        <table>
          <tr>
            <th>Review_ID</th>
            <th>Book_ID</th>
            <th>User_ID</th>
            <th>Likert Scale</th>
            <th>Username</th>
            <th>Review</th>
            <th>Action</th>
          </tr>
          <?php
          $query = "SELECT r.review_id, r.Book_ID, r.User_ID, r.likert_scale, r.review, u.username
          FROM Review r
          INNER JOIN users u ON r.user_ID = u.user_ID
          INNER JOIN students_professors sp ON u.user_ID = sp.user_ID
          INNER JOIN school_unit_manager sm ON sp.school_ID = sm.school_ID
          WHERE r.approved = '0' AND sm.manager_ID = '$managerID'";
          $result = mysqli_query($conn, $query);
          if (!$result) {
              echo "Error fetching reviews: " . mysqli_error($conn);
              exit;
          }

          while ($row = mysqli_fetch_assoc($result)):
          ?>
              <tr>
                <td><?php echo $row['review_id']; ?></td>
                <td><?php echo $row['Book_ID']; ?></td>
                <td><?php echo $row['User_ID']; ?></td>
                <td><?php echo $row['likert_scale']; ?></td>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['review']; ?></td>
                <td>
                  <form action="manager.php" method="POST">
                    <input type="hidden" name="review_id" value="<?php echo $row['review_id']; ?>">
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
