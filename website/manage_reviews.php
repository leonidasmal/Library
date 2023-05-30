<?php
include("connect.php");
session_start();
$managerID = $_SESSION['Manager_ID'];

var_dump($managerID);
if (isset($_POST['approve'])) {
  $reviewID = $_POST['review_id'];
  $query = "UPDATE reviews SET approved='1' WHERE review_id='$reviewID'";
  $result = mysqli_query($conn, $query);

  if ($result) {
      // Review approved update successful
      header("Location: manager.php");
      echo '<script type="text/javascript">';
      echo 'alert("Review Approved!");';
      echo 'window.location.reload();';
      echo '</script>';
  } else {
      // Error updating review approved
      echo "Error approving review: " . mysqli_error($conn);
  }
}

if (isset($_POST['deny'])) {
    $reviewID = $_POST['review_id'];

    // Delete the review from the reviews table
    $deleteQuery = "DELETE FROM reviews WHERE review_id='$reviewID'";
    $deleteResult = mysqli_query($conn, $deleteQuery);
    if ($deleteResult) {
        header("Location: manager.php");
        echo '<script type="text/javascript">';
        echo 'alert("Review denied ");';
        echo 'window.location.reload();';
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

    <title>Manager Approval</title>
  </head>
  <body>
    <div class="center">
      <div class="container">
        <h1>Review Approval</h1>
        <table>
          <tr>
            <th>review_id</th>
            <th>Book_id</th>
            <th>User_ID</th>
            <th>Manager_ID</th>
            <th>likert_scale</th>
            <th>review_status</th>
            <th>Action</th>
          </tr>
          <?php
          $query = "SELECT r.review_id, r.Book_id, r.User_ID, r.Manager_ID, r.likert_scale, r.review_status
          FROM Review r
          WHERE r.Manager_ID = '$managerID'";
            $result = mysqli_query($conn, $query);
            if (!$result) {
                echo "Error fetching reviews: " . mysqli_error($conn);
                exit;
            }

            // Display the data
            echo "<table>";
            echo "<tr>
                    <th>review_id</th>
                    <th>Book_id</th>
                    <th>User_ID</th>
                    <th>Manager_ID</th>
                    <th>likert_scale</th>
                    <th>review_status</th>
                    <th>Action</th>
                </tr>";

            while ($row = mysqli_fetch_assoc($result)) {
                echo "<tr>";
                echo "<td>" . $row['review_id'] . "</td>";
                echo "<td>" . $row['Book_id'] . "</td>";
                echo "<td>" . $row['User_ID'] . "</td>";
                echo "<td>" . $row['Manager_ID'] . "</td>";
                echo "<td>" . $row['likert_scale'] . "</td>";
                echo "<td>" . $row['review_status'] . "</td>";
                echo "<td><a href='edit_review.php?review_id=" . $row['review_id'] . "'>Edit</a></td>";
                echo "</tr>";
            }

            echo "</table>";

          while ($row = mysqli_fetch_assoc($result)):
              ?>
              <tr>
                <td><?php echo $row['review_id']; ?></td>
                <td><?php echo $row['username']; ?></td>
                <td><?php echo $row['review_text']; ?></td>
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
