<!DOCTYPE html>
<html>
<head>
  <title>Review Overview</title>
  <style>
    h1 {
      text-align: center;
    }

    table {
      width: 80%;
      margin-left: auto;
      margin-right: auto;
    }

    th, td {
      border: 1px solid #dddddd;
      text-align: left;
      padding: 8px;
      white-space: nowrap; /* Prevent table cell content from wrapping */
      overflow: hidden; /* Hide content if it exceeds cell width */
      text-overflow: ellipsis; /* Add ellipsis (...) if content overflows */
    }

    th {
      background-color: #f2f2f2;
    }

    .clickable {
      cursor: pointer;
      color: black;
      text-decoration: underline;
    }

    .delete-button {
      padding: 5px 10px;
      background-color: #4CAF50;
      color: white;
      border: none;
      cursor: pointer;
    }
  </style>
  <script>
    function confirmDeleteReview(reviewID) {
      var result = confirm("Are you sure you want to delete this review?");
      if (result) {
        document.getElementById("delete_review_" + reviewID).submit();
      }
    }

    function showPendingReviews() {
      var table = document.getElementById("review-table");
      var rows = table.getElementsByTagName("tr");
      var button = document.getElementById("toggle-button");

      for (var i = 1; i < rows.length; i++) {
        var approvedCell = rows[i].getElementsByTagName("td")[5];

        if (approvedCell.innerHTML !== "Pending") {
          rows[i].style.display = "none";
        } else {
          rows[i].style.display = "";
        }
      }

      if (button.innerHTML === "Show Pending Reviews") {
        button.innerHTML = "Show All Reviews";
      } else {
        button.innerHTML = "Show Pending Reviews";
        for (var i = 1; i < rows.length; i++) {
          rows[i].style.display = "";
        }
      }
    }
  </script>
</head>
<body>
  <h1>Review Overview</h1>
  <?php
  include("connect.php");
  session_start();

  if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST["review_id"])) {
    $reviewID = $_POST["review_id"];
  
    // Update the 'approved' value in the database
    $updateQuery = "UPDATE Review SET approved = 1 WHERE review_id = ?";
    $stmt = $conn->prepare($updateQuery);
    $stmt->bind_param("i", $reviewID);
    $stmt->execute();
  
    // Redirect to the same page or any other desired location
    header("Location: manage_reviews.php");
    exit;
  }

  // Check if the delete form is submitted
  if (isset($_POST['delete_review'])) {
    $reviewID = $_POST['review_id'];

    // Delete the review record from the database
    $deleteReviewQuery = "DELETE FROM Review WHERE review_id = ?";
    $deleteReviewStmt = mysqli_prepare($conn, $deleteReviewQuery);
    mysqli_stmt_bind_param($deleteReviewStmt, 'i', $reviewID);
    mysqli_stmt_execute($deleteReviewStmt);

    // Redirect to the same page
    header("Location: manage_reviews.php");
    exit;
  }

  if (isset($_SESSION['Manager_ID'])) {
    $ManagerID = $_SESSION['Manager_ID'];

  // Retrieve review data from the database
  $reviewQuery = "SELECT R.review_id, R.likert_scale, R.review, R.approved, U.username, B.title
    FROM Review R
    INNER JOIN Users U ON R.User_ID = U.User_ID
    INNER JOIN Book B ON R.Book_ID = B.Book_ID
    WHERE R.Manager_ID = ?";

  $stmt = $conn->prepare($reviewQuery);
  $stmt->bind_param("i", $ManagerID);
  $stmt->execute();
  $reviewResult = $stmt->get_result();
  }
  ?>


  <button id="toggle-button" onclick="showPendingReviews()">Show Pending Reviews</button>

  <table id="review-table">
    <tr>
      <th>Review ID</th>
      <th>Book Title</th>
      <th>Username</th>
      <th>Likert Scale</th>
      <th>Review</th>
      <th>Approved</th>
    </tr>
    <?php while ($row = mysqli_fetch_assoc($reviewResult)): ?>
      <tr>
        <td><?php echo $row['review_id']; ?></td>
        <td><?php echo $row['title']; ?></td>
        <td><?php echo $row['username']; ?></td>
        <td><?php echo $row['likert_scale']; ?></td>
        <td><?php echo $row['review']; ?></td>
        <td><?php echo ($row['approved'] == 1) ? 'Approved' : 'Pending'; ?></td>
        <td>
          <button class="delete-button" onclick="confirmDeleteReview(<?php echo $row['review_id']; ?>)">Delete</button>
          <form id="delete_review_<?php echo $row['review_id']; ?>" method="post" style="display: none;">
            <input type="hidden" name="review_id" value="<?php echo $row['review_id']; ?>">
            <input type="hidden" name="delete_review" value="1">
          </form>
          <?php if ($row['approved'] == 0): ?>
            <td>
              <form method="post" onsubmit="return confirm('Σίγουρα θες να επιβαιβεώσεις αυτήν την αξιολόγηση;');">
                <input type="hidden" name="review_id" value="<?php echo $row['review_id']; ?>">
                <input type="submit" class="approve-button" value="Approve">
              </form>
            </td>
          <?php endif; ?>
        </td>
      </tr>
    <?php endwhile; ?>
  </table>
  <form action="operator_manager_dashboard.php" method="get">
    <input type="submit" value="Back">
</form>
</body>
</html>
